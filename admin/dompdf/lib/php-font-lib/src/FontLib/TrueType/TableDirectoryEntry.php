<?php

namespace FontLib\TrueType;

use FontLib\Table\DirectoryEntry;
use FontLib\Font;

/**
 * TrueType table directory entry.
 *
 * @package php-font-lib
 */
class TableDirectoryEntry extends DirectoryEntry
{
    /**
     * @var Font The parent Font object.
     */
    protected Font $font;

    /**
     * TableDirectoryEntry constructor.
     *
     * @param Font $font The parent Font object.
     */
    public function __construct(Font $font)
    {
        parent::__construct($font);
        $this->font = $font;
    }

    /**
     * Parse the table directory entry.
     */
    public function parse(): void
    {
        parent::parse();

        $this->checksum = $this->font->readUInt32();
        $this->offset = $this->font->readUInt32();
        $this->length = $this->font->readUInt32();

        // Added error handling for reading table data in case the file pointer is at the end of the file.
        if ($this->offset + $this->length > $this->font->getFileSize()) {
            throw new \RuntimeException('Invalid table directory entry.');
        }
    }
}
