<?php

namespace FontLib\Glyph;

use FontLib\Table\Type\glyf;
use FontLib\TrueType\File;
use FontLib\BinaryStream;

/**
 * `glyf` font table.
 *
 * @package php-font-lib
 */
class Outline
{
    /**
     * @var glyf
     */
    protected $table;

    protected int $offset;
    protected int $size;

    // Data
    public int $numberOfContours;
    public int $xMin;
    public int $yMin;
    public int $xMax;
    public int $yMax;

    public string $raw;

    /**
     * @param glyf       $table
     * @param int        $offset
     * @param int        $size
     * @param BinaryStream $font
     *
     * @return static
     */
    static function init(glyf $table, int $offset, int $size, BinaryStream $font): self
    {
        $font->seek($offset);

        if ($font->readInt16() > -1) {
            /** @var OutlineSimple $glyph */
            $glyph = new OutlineSimple($table, $offset, $size);
        } else {
            /** @var OutlineComposite $glyph */
            $glyph = new OutlineComposite($table, $offset, $size);
        }

        $glyph->parse($font);

        return $glyph;
    }

    /**
     * @return File
     */
    function getFont(): File
    {
        return $this->table->getFont();
    }

    function __construct(glyf $table, int $offset = null, int $size = null)
    {
        $this->table  = $table;
        $this->offset = $offset;
        $this->size   = $size;
    }

    function parse(BinaryStream $font): void
    {
        $font->seek($this->offset);

        if (!$this->size) {
            return;
        }

        $this->raw = $font->read($this->size);
        $this->parseData();
    }

    function parseData(): void
    {
        $font = $this->getFont();
        $font->seek($this->offset);

        $this->numberOfContours = $font->readInt16();
        $this->xMin             = $font->readFWord();
        $this->yMin             = $font->readFWord();
        $this->xMax             = $font->readFWord();
        $this->yMax             = $font->readFWord();
    }

    function encode(): string
    {
        $font = $this->getFont();

        return $font->write($this->raw, strlen($this->raw));
    }

    function getBounds(): array
    {
        return [$this->xMin, $this->yMin, $this->xMax, $this->yMax];
    }

    function getContours(): array
    {
        // Implement this method based on the specific type of outline
    }

    function getGlyphIDs(): array
    {
        return [];
    }
}
