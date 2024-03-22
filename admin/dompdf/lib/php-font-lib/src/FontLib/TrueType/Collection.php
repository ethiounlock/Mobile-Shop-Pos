<?php

namespace FontLib\TrueType;

use Countable;
use FontLib\BinaryStream;
use Iterator;
use OutOfBoundsException;

/**
 * TrueType collection font file.
 *
 * @package php-font-lib
 */
class Collection extends BinaryStream implements Iterator, Countable {
  /**
   * Current iterator position.
   *
   * @var int
   */
  private $position = 0;

  /**
   * Offsets of each font in the collection.
   *
   * @var array
   */
  protected $collectionOffsets = [];

  /**
   * Collection of FontLib\TrueType\File objects.
   *
   * @var array
   */
  protected $collection = [];

  /**
   * Version of the collection.
   *
   * @var int
   */
  protected $version;

  /**
   * Number of fonts in the collection.
   *
   * @var int
   */
  protected $numFonts;

  /**
   * FontLib\BinaryStream object.
   *
   * @var BinaryStream
   */
  protected $f;

  /**
   * Collection constructor.
   *
   * @param BinaryStream $f
   */
  public function __construct(BinaryStream $f) {
    $this->f = $f;
  }

  /**
   * Parse the collection.
   */
  public function parse() {
    if ($this->numFonts !== null) {
      return;
    }

    $this->f->read(4); // tag name

    $this->version  = $this->f->readFixed();
    $this->numFonts = $this->f->readUInt32();

    for ($i = 0; $i < $this->numFonts; $i++) {
      $this->collectionOffsets[] = $this->f->readUInt32();
    }
  }

  /**
   * Get a font from the collection.
   *
   * @param int $fontId
   *
   * @return File
   * @throws OutOfBoundsException
   */
  public function getFont(int $fontId): File {
    $this->parse();

    if ($fontId < 0 || $fontId >= $this->numFonts) {
      throw new OutOfBoundsException();
    }

    if (isset($this->collection[$fontId])) {
      return $this->collection[$fontId];
    }

    if ($
