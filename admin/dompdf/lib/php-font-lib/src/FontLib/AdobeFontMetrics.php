<?php

declare(strict_types=1);

namespace FontLib;

use FontLib\Table\Type\name;
use FontLib\TrueType\File;
use FontLib\EncodingMap;

/**
 * Adobe Font Metrics file creation utility class.
 */
class AdobeFontMetrics
{
    private File $font;
    private ?string $f = null;

    function __construct(File $font)
    {
        $this->font = $font;
    }

    function write(string $file, ?string $encoding = null): void
    {
        $map_data = [];

        if ($encoding) {
            $encoding = preg_replace("/[^a-z0-9-_]/", "", $encoding);
            $map_file = dirname(__FILE__) . "/../maps/$encoding.map";

            if (!file_exists($map_file)) {
                throw new \Exception("Unkown encoding ($encoding)");
            }

            $map = new EncodingMap($map_file);
            $map_data = $map->parse();
        }

        $this->f = fopen($file, "w+");

        $font = $this->font;

        $this->startSection("FontMetrics", 4.1);
        $this->addPair("Notice", "Converted by PHP-font-lib");
        $this->addPair("Comment", "https://github.com/PhenX/php-font-lib");

        $encoding_scheme = ($encoding ?? "FontSpecific");
        $this->addPair("EncodingScheme", $encoding_scheme);

        $records = $font->getData("name", "records");
        foreach ($records as $id => $record) {
            if (!isset(name::$nameIdCodes[$id]) || preg_match("/[\r\n]/", $record->string)) {
                continue;
            }

            $this->addPair(name::$nameIdCodes[$id], $record->string);
        }

        $os2 = $font->getData("OS/2");
        $this->addPair("Weight", ($os2["usWeightClass"] > 400 ? "Bold" : "Medium"));

        $post = $font->getData("post");
        $this->addPair("ItalicAngle", $post["italicAngle"]);
        $this->addPair("IsFixedPitch", ($post["isFixedPitch"] ? "true" : "false"));
        $this->addPair("UnderlineThickness", $font->normalizeFUnit($post["underlineThickness"]));
        $this->addPair("UnderlinePosition", $font->normalizeFUnit($post["underlinePosition"]));

        $hhea = $font->getData("hhea");

        if (isset($hhea["ascent"])) {
            $this->addPair("FontHeightOffset", $font->normalizeFUnit($hhea["lineGap"]));
            $this->addPair("Ascender", $font->normalizeFUnit($hhea["ascent"]));
            $this->addPair("Descender", -abs($font->normalizeFUnit($hhea["descent"])));
        } else {
            $this->addPair("FontHeightOffset", $font->normalizeFUnit($os2["typoLineGap"]));
            $this->addPair("Ascender", $font->normalizeFUnit($os2["typoAscender"]));
            $this->addPair("Descender", -abs($font->normalizeFUnit($os2["typoDescender"] ?? 0)));
        }

        $head = $font->getData("head");
        $this->addArray("FontBBox", [
            $font->normalizeFUnit($head["xMin"]),
            $font->normalizeFUnit($head["yMin"]),
            $font->normalizeFUnit($head["xMax"]),
            $font->normalizeFUnit($head["yMax"]),
        ]);

        $glyphIndexArray = $font->getUnicodeCharMap();

        if ($glyphIndexArray) {
            $hmtx = $font->getData("hmtx");
            $names = $font->getData("post", "names");

            $this->startSection("CharMetrics", count($hmtx));

            if ($encoding) {
                foreach ($map_data as $code => $value) {
                    [$c, $name] = $value;

                    if (!isset($glyphIndexArray[$c])) {
                        continue;
                    }

                    $g = $glyphIndexArray[$c];

                    if (!isset($hmtx[$g])) {
                        $hmtx[$g] = $hmtx[0];
                    }

                    $this->addMetric([
                        "C"  => ($code > 255 ? -1 : $code),
                        "WX" => $font->normalizeFUnit($hmtx[$g][0]),
                        "N"  => $name,
                    ]);
                }
            } else {
                foreach ($glyphIndexArray as $c => $g) {
                    if (!isset($hmtx[$g])) {
                        $hmtx[$g] = $hmtx[0];
                    }

                    $this->addMetric([
                        "U"  => $c,
                        "WX" => $font->normalizeFUnit($hmtx[$g][0]),
                        "N"  => (isset($names[$g]) ? $names[$g] : sprintf("uni%04x", $c)),
                        "G"  => $g,
                    ]);
                }
            }

            $this->endSection("
