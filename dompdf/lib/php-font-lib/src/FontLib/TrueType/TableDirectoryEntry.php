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
     *
     * @return void
     */
    public function parse(): void
    {
        parent::parse();

        $font = $this->font;

        $font->seek($this->getOffset());

        if ($font->eof()) {
            throw new \RuntimeException('Failed to read table data.');
        }

        $this->checksum = $font->readUInt32();
        $this->offset = $font->readUInt32();
        $this->length = $font->readUInt32();
    }

    /**
     * Get the offset of the table data.
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
