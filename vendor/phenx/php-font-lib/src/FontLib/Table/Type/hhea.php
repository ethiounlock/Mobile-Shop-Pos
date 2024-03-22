<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * Class hhea
 * @package FontLib\Table\Type
 *
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @link    https://github.com/PhenX/php-font-lib
 * @package php-font-lib
 */
class hhea extends Table
{
    /**
     * @var array
     */
    protected $def = [
        "version" => self::Fixed,
        "ascent" => self::FWord,
        "descent" => self::FWord,
        "lineGap" => self::FWord,
        "advanceWidthMax" => self::uFWord,
        "minLeftSideBearing" => self::FWord,
        "minRightSideBearing" => self::FWord,
        "xMaxExtent" => self::FWord,
        "caretSlopeRise" => self::int1
