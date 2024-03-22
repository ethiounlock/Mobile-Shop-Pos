<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * `maxp` font table.
 *
 * @package php-font-lib
 */
class maxp extends Table
{
    /**
     * @var array
     */
    protected $def = [
        "version" => FontLib\Table\Table::Fixed,
        "numGlyphs" => FontLib\Table\Table::uint16,
        "maxPoints" => FontLib\Table\Table::uint16,
        "maxContours" => FontLib\Table\Table::uint16,
        "maxComponentPoints" => FontLib\Table\Table::uint16,
        "maxComponentContours" => FontLib\Table\Table::uint16,
        "maxZones" => FontLib\Table\Table::uint16,
        "maxTwilightPoints" => FontLib\Table\Table::uint16,
        "maxStorage" => FontLib\Table\Table::uint16,
        "maxFunctionDefs" => FontLib\Table\Table::uint16,
        "maxInstructionDefs" => FontLib\Table\Table::uint16,
        "maxStackElements" => FontLib\Table\Table::uint16,
        "maxSizeOfInstructions" => FontLib\Table\Table::uint16,
        "maxComponentElements" => FontLib\Table\Table::uint16,
        "maxComponentDepth" => FontLib\Table\Table::uint16,
    ];

    /**
     *
