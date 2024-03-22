<?php

declare(strict_types=1);

namespace FontLib\Table\Type;

use FontLib\Font;
use FontLib\Table\Table;

/**
 * `name` is a font table that contains information about the font, such as its name, version, and copyright information.
 *
 * @package php-font-lib
 */
class name extends Table
{
    private static $header_format = [
        'format' => self::UINT16,
        'count' => self::UINT16,
        'stringOffset' => self::UINT16,
    ];

    const NAME_COPYRIGHT = 0;
    const NAME_NAME = 1;
    const NAME_SUBFAMILY = 2;
    const NAME_SUBFAMILY_ID = 3;
    const NAME_FULL_NAME = 4;
    const NAME_VERSION = 5;
    const NAME_POSTSCRIPT_NAME = 6;
    const NAME_TRADEMARK = 7;
    const NAME_MANUFACTURER = 8;
    const NAME_DESIGNER = 9;
    const NAME_DESCRIPTION = 10;
    const NAME_VENDOR_URL = 11;
    const NAME_DESIGNER_URL = 12;
    const NAME_LICENSE = 13;
    const NAME_LICENSE_URL = 14;
    const NAME_PREFERRE_FAMILY = 16;
    const NAME_PREFERRE_SUBFAMILY = 17;
    const NAME_COMPAT_FULL_NAME = 18;
    const NAME_SAMPLE_TEXT = 19;

    /**
     * A map of name ID codes to their corresponding names.
     *
     * @var array
     */
    public static $nameIdCodes = [
        0 => 'Copyright',
        1 => 'FontName',
        2 => 'FontSubfamily',
        3 => 'UniqueID',
        4 => 'FullName',
        5 => 'Version',
        6 => 'PostScriptName',
        7 => 'Trademark',
        8 => 'Manufacturer',
        9 => 'Designer',
        10 => 'Description',
        11 => 'FontVendorURL',
        12 => 'FontDesignerURL',
        13 => 'LicenseDescription',
        14 => 'LicenseURL',
        // 15
        16 => 'PreferredFamily',
        17 => 'PreferredSubfamily',
        18 => 'CompatibleFullName',
        19 => 'SampleText',
    ];

    /**
     * An array of platforms that names can be associated with.
     *
     * @var array
     */
    public static $platforms = [
        0 => 'Unicode',
        1 => 'Macintosh',
        // 2 =>  Reserved
        3 => 'Microsoft',
    ];

    /**
     * An array of platform-specific information for a given platform.
     *
     * @param int $platform
     *
     * @return array
     */
    public static function getPlatformSpecific(int $platform): array
    {
        switch ($platform) {
            case 0:
                return [
                    0 => 'Default semantics',
                    1 => 'Version 1.1 semantics',
                    2 => 'ISO 10646 1993 semantics (deprecated)',
                    3 => 'Unicode 2.0 or later semantics',
                ];
            case 1:
                return [
                    0 => 'Roman',
                    1 => 'Japanese',
                    2 => 'Traditional Chinese',
                    3 => 'Korean',
                    4 => 'Arabic',
                    5 => 'Hebrew',
                    6 => 'Greek',
                    7 => 'Russian',
                    8 => 'RSymbol',
                    9 => 'Devanagari',
                    10 => 'Gurmukhi',
                    11 => 'Gujarati',
                    12 => 'Oriya',
                    13 => 'Bengali',
                    14 => 'Tamil',
                    15 => 'Telugu',
                    16 => 'Kannada',
                    17 => 'Malayalam',
                    18 => 'Sinhalese',
                    19 => 'Burmese',
                    20 => 'Khmer',
                    21 => 'Thai',
                    22 => 'Laotian',
                    23 => 'Georgian',
                    24 => 'Armenian',
                    25 => 'Simplified Chinese',
                    26 => 'Tibetan',
                    27 => 'Mongolian',
                    28 => 'Geez',
                    29 => 'Slavic',
                    30 => 'Vietnamese',
                    31 => 'Sindhi',
                ];
            case 3:
                return [
                    0 => 'Symbol',
                    1 => 'Unicode BMP (UCS-2)',
                    2 => 'ShiftJIS',
                    3 => 'PRC',
                    4 => 'Big5',
                    5 => 'Wansung',
                    6 => 'Johab',
                    //  7 => Reserved
                    //  8
