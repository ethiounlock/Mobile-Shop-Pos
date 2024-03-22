<?php

namespace FontLib\WOFF;

use FontLib\Table\DirectoryEntry;

/**
 * WOFF font file table directory entry.
 *
 * @package php-font-lib
 */
class TableDirectoryEntry extends DirectoryEntry
{
    /**
     * @var File|null
     */
    private $font;

    public int $origLength;

    /**
     * TableDirectoryEntry constructor.
     *
     * @param File $font
     */
    public function __construct(File $font) {
        $this->font = $font;
        parent::__construct($font);
    }

    /**
     * Parse the table directory entry.
     */
    public function parse(): void {
        parent::parse();

        if (is_null($this->font)) {
            throw new \RuntimeException('Font object is not set.');
        }

        $this->offset     = $this->font->readUInt32();
        $this->length     = $this->font->readUInt32();
        $this->origLength = $this->font->readUInt32();
        $this->checksum   = $this->font->readUInt32();
    }
}
