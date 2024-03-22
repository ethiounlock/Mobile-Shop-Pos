<?php

declare(strict_types=1);

namespace FontLib\Table\Type;

use FontLib\Table\Table;
use FontLib\TrueType\File;

/**
 * @package php-font-lib
 */
abstract class Table
{
    /**
     * @var array
     */
    protected array $def;

    /**
     * @var File
     */
    protected File $font;

    /**
     * @var array
     */
    protected array $data;

    /**
     * `post` font table.
     *
     * @package php-font-lib
     */
    final class post extends Table
    {
        protected array $def = [
            "format" => self::Fixed,
            "italicAngle" => self::Fixed,
            "underlinePosition" => self::FWord,
            "underlineThickness" => self::FWord,
            "isFixedPitch" => self::uint32,
            "minMemType42" => self::uint32,
            "maxMemType42" => self::uint32,
            "minMemType1" => self::uint32,
            "maxMemType1" => self::uint32,
        ];

        /**
         * @param File $font
         */
        public function __construct(File $font)
        {
            $this->font = $font;
        }

        /**
         * @return void
         */
        protected function _parse(): void
        {
            $data = $this->font->unpack($this->def);

            $names = [];

            switch ($data["format"]) {
                case 1:
                    $names = File::$macCharNames;
                    break;

                case 2:
                    $data["numberOfGlyphs"] = $this->font->readUInt16();

                    $glyphNameIndex = $this->font->readUInt16Many($data["numberOfGlyphs"]);

                    $data["glyphNameIndex"] = $glyphNameIndex;

                    for ($i = 0; $i < $data["numberOfGlyphs"]; $i++) {
                        $len = $this->font->readUInt8();
                        $names[] = $this->font->read($len);
                    }

                    foreach ($glyphNameIndex as $g => $index) {
                        if ($index < 258) {
                            $names[$g] = File::$macCharNames[$index] ?? '';
                        } else {
                            $names[$g] = $names[$index - 258] ?? '';
                        }
                    }

                    break;

                case 2.5:
                    // TODO
                    break;

                case 3:
                    // nothing
                    break;

                case 4:
                    // TODO
                    break;

                default:
                    // TODO: handle other cases
                    break;
            }

            $data["names"] = $names;

            $this->data = $data;
        }

        /**
         * @return int
         */
        public function _encode(): int
        {
            $data = $this->data;
            $data["format"] = 3;

            return $this->font->pack($this->def, $data);
        }
    }
}
