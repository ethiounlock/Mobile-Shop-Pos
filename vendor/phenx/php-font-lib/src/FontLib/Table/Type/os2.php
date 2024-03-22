<?php

/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace FontLib\Table\Type;
use FontLib\Table\Table;

/**
 * OS/2 font table class.
 *
 * @package php-font-lib
 */
class Os2 extends Table
{
    protected $def = [
        'version' => self::UINT16,
        'xAvgCharWidth' => self::INT16,
        'usWeightClass' => self::UINT16,
        'usWidthClass' => self::UINT16,
        'fsType' => self::INT16,
        'ySubscriptXSize' => self::INT16,
        'ySubscriptYSize' => self::INT16,
        'ySubscriptXOffset' => self::INT16,
        'ySubscriptYOffset' => self::INT16,
        'ySuperscriptXSize' => self::INT16,
        'ySuperscriptYSize' => self::INT16,
        'ySuperscriptXOffset' => self::INT16,
        'ySuperscriptYOffset' => self::INT16,
        'yStrikeoutSize' => self::INT16,
        'yStrikeoutPosition' => self::INT16,
        'sFamilyClass' => self::INT16,
        'panose' => [self::UINT8, 10],
        'ulCharRange' => [self::UINT32, 4],
        'achVendID' => [self::CHAR, 4],
        'fsSelection' => self::UINT16,
        'fsFirstCharIndex' => self::UINT16,
        'fsLastCharIndex' => self::UINT16,
        'typoAscender' => self::INT16,
        'typoDescender' => self::INT16,
        'typoLineGap' => self::INT16,
        'winAscent' => self::INT16,
        'winDescent' => self::INT16,
    ];
}
