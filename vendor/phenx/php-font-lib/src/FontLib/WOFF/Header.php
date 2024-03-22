<?php
/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace FontLib\WOFF;

/**
 * WOFF font file header.
 *
 * @package php-font-lib
 */
class Header extends \FontLib\TrueType\Header
{
  protected $def = [
    "format"         => self::UINT32,
    "flavor"         => self::UINT32,
    "length"         => self::UINT32,
    "numTables"      => self::UINT16,
    self::UINT16, // padding
    "totalSfntSize"  => self::UINT32,
    "majorVersion"   => self::UINT16,
    "minorVersion"   => self::UINT16,
    "metaOffset"     => self::UINT32,
    "metaLength"     => self::UINT32,
    "metaOrigLength" => self::UINT32,
    "privOffset"     => self::UINT32,
    "privLength"     => self::UINT32,
  ];

  const UINT16 = 1;
  const UINT32 = 2;
}
