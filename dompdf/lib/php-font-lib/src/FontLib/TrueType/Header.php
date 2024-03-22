<?php
/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

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

    public const FORMAT_STRINGS = [
        0 => 'No specific format',
        1 => 'TrueType',
        2 => 'TrueType with collections',
        3 => 'CFF with collections',
    ];

    /**
     * Convert uint32 to corresponding string format.
     *
     * @param int $uint32
     * @return string
     */
    protected function convertUInt32ToStr(int $uint32): string
    {
        return self::FORMAT_STRINGS[$uint32] ?? 'Unknown format';
    }

    public function parse(): void
    {
        parent::parse();

        $format = $this->data["format"];
        $this->data["formatText"] = $this->convertUInt32ToStr($format);
    }
}
