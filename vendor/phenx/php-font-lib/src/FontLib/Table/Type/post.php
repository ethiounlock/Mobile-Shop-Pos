<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;
use FontLib\TrueType\File;

/**
 * `post` font table.
 *
 * @package php-font-lib
 */
class post extends Table {
  /**
   * @var array
   */
  protected $def = [
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
   * @var array
   */
  protected $names = [];

  /**
   * {@inheritdoc}
   */
  protected function _parse(): void {
    $font = $this->font ?? throw new \RuntimeException('Font object not found');
    $data = $font->unpack($this->def);

    switch ($data["format"]) {
      case 1:
        $this->names = File::$macCharNames;
        break;

      case 2:
        $data["numberOfGlyphs"] = $font->readUInt16();

        $glyphNameIndex = $font->readUInt16Many($data["numberOfGlyphs"]);

        $data["glyphNameIndex"] = $glyphNameIndex;

        $namesPascal = [];
        for ($i = 0; $i < $data["numberOfGlyphs"]; $i++) {
          $len = $font->readUInt8();
          $namesPascal[] = $font->read($len);
        }

        foreach ($glyphNameIndex as $g => $index) {
          if ($index < 258) {
            $this->names[$g] = File::$macCharNames[$index];
          }
          else {
            $this->names[$g] = $namesPascal[$index - 258];
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
        // throw an exception for unsupported format
        throw new \RuntimeException('Unsupported post format: ' . $data["format"]);
    }

    $this->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function _encode(): int {
    $font = $this->font ?? throw new \RuntimeException('Font object not found');
    $data = $this->data;
    $data["format"] = 3;

    return $font->pack($this->def, $data);
  }
}
