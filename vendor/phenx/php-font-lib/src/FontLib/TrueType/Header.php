<?php

namespace FontLib\TrueType;

use FontLib\Header;

/**
 * TrueType font file header.
 *
 * @package php-font-lib
 */
class Header extends Header
{
    protected array $def = [
        "format"        => self::UINT32,
        "numTables"     => self::UINT16,
        "searchRange"   => self::UINT16,
        "entrySelector" => self::UINT16,
        "rangeShift"    => self::UINT16,
    ];

    public const FORMAT_STRING_CONVERSION = [
        0 => 'None',
        1 => 'TrueType',
        2 => 'TrueType Collection (CFF)',
    ];

    public function parse(): void
    {
        parent::parse();

        $format = $this->data['format'];
        $this->data['formatText'] = self::convertUInt32ToStr($format);
    }

    /**
     * Convert a uint32 value to its string representation.
     *
     * @param int $uint32 The uint32 value to convert.
     *
     * @return string The string representation of the uint32 value.
     */
    protected static function convertUInt32ToStr(int $uint32): string
    {
        return self::FORMAT_STRING_CONVERSION[$uint32] ?? 'Unknown';
    }
}
