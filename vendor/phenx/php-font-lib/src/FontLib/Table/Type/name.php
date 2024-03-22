<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;
use FontLib\Font;

/**
 * `name` font table.
 *
 * @package php-font-lib
 */
class name extends Table
{
    private const NAME_COPYRIGHT = 0;
    private const NAME_NAME = 1;
    private const NAME_SUBFAMILY = 2;
    private const NAME_SUBFAMILY_ID = 3;
    private const NAME_FULL_NAME = 4;
    private const NAME_VERSION = 5;
    private const NAME_POSTSCRIPT_NAME = 6;
    private const NAME_TRADEMARK = 7;
    private const NAME_MANUFACTURER = 8;
    private const NAME_DESIGNER = 9;
    private const NAME_DESCRIPTION = 10;
    private const NAME_VENDOR_URL = 11;
    private const NAME_DESIGNER_URL = 12;
    private const NAME_LICENSE = 13;
    private const NAME_LICENSE_URL = 14;
    private const NAME_PREFERRE_FAMILY = 16;
    private const NAME_PREFERRE_SUBFAMILY = 17;
    private const NAME_COMPAT_FULL_NAME = 18;
    private const NAME_SAMPLE_TEXT = 19;

    private const NAME_ID_CODES = [
        0 => "Copyright",
        1 => "FontName",
        2 => "FontSubfamily",
        3 => "UniqueID",
        4 => "FullName",
        5 => "Version",
        6 => "PostScriptName",
        7 => "Trademark",
        8 => "Manufacturer",
        9 => "Designer",
        10 => "Description",
        11 => "FontVendorURL",
        12 => "FontDesignerURL",
        13 => "LicenseDescription",
        14 => "LicenseURL",
        16 => "PreferredFamily",
        17 => "PreferredSubfamily",
        18 => "CompatibleFullName",
        19 => "SampleText",
    ];

    private const PLATFORMS = [
        0 => "Unicode",
        1 => "Macintosh",
        3 => "Microsoft",
    ];

    private const PLATFORM_SPECIFIC = [
        // Unicode
        0 => [
            0 => "Default semantics",
            1 => "Version 1.1 semantics",
            2 => "ISO 10646 1993 semantics (deprecated)",
            3 => "Unicode 2.0 or later semantics",
        ],

        // Macintosh
        1 => [
            0 => "Roman",
            1 => "Japanese",
            2 => "Traditional Chinese",
            3 => "Korean",
            4 => "Arabic",
            5 => "Hebrew",
            6 => "Greek",
            7 => "Russian",
            8 => "RSymbol",
            9 => "Devanagari",
            10 => "Gurmukhi",
            11 => "Gujarati",
            12 => "Oriya",
            13 => "Bengali",
            14 => "Tamil",
            15 => "Telugu",
            16 => "Kannada",
            17 => "Malayalam",
            18 => "Sinhalese",
            19 => "Burmese",
            20 => "Khmer",
            21 => "Thai",
            22 => "Laotian",
            23 => "Georgian",
            24 => "Armenian",
            25 => "Simplified Chinese",
            26 => "Tibetan",
            27 => "Mongolian",
            28 => "Geez",
            29 => "Slavic",
            30 => "Vietnamese",
            31
