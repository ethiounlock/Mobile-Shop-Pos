<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * `loca` font table.
 *
 * The `loca` (Location) table maps glyph indexes to their locations in the `glyf` table.
 * It contains an array of offsets to the glyph outlines in the `glyf` table.
 *
 * @package php-font-lib
 */
class loca extends Table
{
    /**
     * @var int[]
     */
    protected array $data = [];

    /**
     * @inheritDoc
     */
    protected function _parse(): void
    {
        $font = $this->getFont();
        $offset = $font->pos();

        $indexToLocFormat = $font->getData("head", "indexToLocFormat");
        $numGlyphs = $font->getData("maxp", "numGlyphs");

        $font->seek($offset);

        $data = [];

        // 2 bytes
        if ($indexToLocFormat === 0) {
            $d = $font->read(($numGlyphs + 1) * 2);
            $loc = unpack("n*", $d);

            for ($i = 0; $i <= $numGlyphs; $i++) {
                $data[] = $loc[$i + 1] * 2 ?? 0;
            }
        }

        // 4 bytes
        else if ($indexToLocFormat === 1) {
            $d = $font->read(($numGlyphs + 1) * 4);
            $loc = unpack("N*", $d);

            for ($i = 0; $i <= $numGlyphs; $i++) {
                $data[] = $loc[$i + 1] ?? 0;
            }
        }

        // Handle invalid `indexToLocFormat` values
        else {
            throw new \InvalidArgumentException('Invalid `indexToLocFormat` value: ' . $indexToLocFormat);
        }

        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function _encode(): int
    {
        $font = $this->getFont();
        $data = $this->data;

        $indexToLocFormat = $font->getData("head", "indexToLocFormat");
        $numGlyphs = $font->getData("maxp", "numGlyphs");
        $length = 0;

       
