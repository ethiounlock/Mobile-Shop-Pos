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

  protected array $collectionOffsets = [];
  protected array $collection = [];
  protected ?int $version = null;
  protected int $numFonts;
  protected ?BinaryStream $f;

  function parse(): void {
    if ($this->numFonts !== null) {
      return;
    }

    $this->read(4); // tag name

    $this->version  = $this->readFixed();
    $this->numFonts = $this->readUInt32();

    for ($i = 0; $i < $this->numFonts; $i++) {
      $this->collectionOffsets[] = $this->readUInt32();
    }
  }

  /**
   * @param int $fontId
   *
   * @throws OutOfBoundsException
   * @return File
   */
  function getFont(int $fontId): File {
    $this->parse();

    if (!isset($this->collectionOffsets[$fontId])) {
      throw new OutOfBoundsException();
    }

    if (isset($this->collection[$fontId])) {
      return $this->collection[$fontId];
    }

    $font = new File();

    if ($this->f === null) {
      throw new \RuntimeException('f property is not set');
    }

    $font->f = $this->f;
    $font->setTableOffset($this->collectionOffsets[$fontId]);

    return $this->collection[$fontId] = $font;
  }

  function current(): File {
    return $this->getFont($this->position);
  }

  function key(): int {
    return $this->position;
  }

  function next(): void {
    ++$this->position;
  }

  function rewind(): void {
    $this->position = 0;
  }

  function valid(): bool {
    $this->parse();

    return isset($this->collectionOffsets[$this->position]);
  }

  function count(): int {
    $this
