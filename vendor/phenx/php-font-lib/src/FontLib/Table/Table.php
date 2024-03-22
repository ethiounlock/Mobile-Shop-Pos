<?php

declare(strict_types=1);

namespace FontLib\Table;

use FontLib\Font;
use FontLib\TrueType\File;
use FontLib\BinaryStream;

/**
 * Generic font table.
 *
 * @package php-font-lib
 */
class Table extends BinaryStream
{
    /**
     * @var DirectoryEntry
     */
    protected $entry;

    protected array $def = [];

    public $data;

    /**
     * Table constructor.
     *
     * @param DirectoryEntry $entry
     */
    final public function __construct(DirectoryEntry $entry)
    {
        $this->entry = $entry;
        $entry->setTable($this);
        $this->def = $this->getDefinition();
    }

    /**
     * @return File
     */
    final public function getFont(): Font
    {
        return $this->entry->getFont() ?? throw new \RuntimeException('Font object is not set');
    }

    /**
     * @return array
     */
    abstract protected function getDefinition(): array;

    protected function _encode(): int
    {
        if (empty($this->data)) {
            Font::d('  >> Table is empty');

            return 0;
        }

        return $this->getFont()->pack($this->def, $this->data);
    }

    protected function _parse(): void
    {
        $this->data = $this->getFont()->unpack($this->def);
    }

    protected function _parseRaw(): void
    {
        $this->data = $this->getFont()->read($this->entry->length);
    }

    protected function _encodeRaw(): string
    {
        return $this->getFont()->write($this->data, $this->entry->length);
    }

    final public function encode(): int
    {
        $this->entry->startWrite();

        if (false && empty($this->def)) {
            $length = $this->_encodeRaw();
        } else {
            $length = $this->_encode();
        }

        $this->entry->endWrite();

        return $length;
    }

    final public function parse(): void
    {
        $this->entry->startRead();

        if (false && empty($this->def
