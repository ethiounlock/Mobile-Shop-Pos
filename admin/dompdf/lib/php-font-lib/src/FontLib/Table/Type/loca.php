<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * `loca` font table.
 *
 * @package php-font-lib
 */
class Loca extends Table
{
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
                $data[] = isset($loc[$i + 1]) ?  $loc[$i + 1] * 2 : 0;
            }
        }

        // 4 bytes
        else if ($indexToLocFormat === 1) {
            $d = $font->read(($numGlyphs + 1) * 4);
            $loc = unpack("N*", $d);

            for ($i = 0; $i <= $numGlyphs; $i++) {
                $data[] = isset($loc[$i + 1]) ? $loc[$i + 1] : 0;
            }
        }

        else {
            throw new \InvalidArgumentException("Invalid indexToLocFormat value: {$indexToLocFormat}");
        }

        $this->data = $data;
    }

    function _encode(): int
    {
        $font = $this->getFont();
        $data = $this->data;

        $indexToLocFormat = $font->getData("head", "indexToLocFormat");
        $numGlyphs = $font->getData("maxp", "numGlyphs");
        $length = 0;

        // 2 bytes
        if ($indexToLocFormat === 0) {
            for ($i = 0; $i <= $numGlyphs; $i++) {
                $length += $font->writeUInt16($data[$i] / 2);
            }
        }

        // 4 bytes
        else if ($indexToLocFormat === 1) {
            for ($i = 0; $i <= $numGlyphs; $i++) {
                $length += $font->writeUInt32($data[$i
