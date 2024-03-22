<?php

namespace FontLib;

use FontLib\Exception\FontNotFoundException;
use FontLib\Stream;
use FontLib\Iconv;

/**
 * Generic font file.
 *
 * @package php-font-lib
 */
class Font {
  static $debug = false;

  /**
   * @param string $file The font file
   *
   * @return TrueType\File|OpenType\File|WOFF\File|TrueType\Collection|EOT\File|null $file
   */
  public static function load(string $file) {
    if(!file_exists($file)){
        throw new FontNotFoundException($file);
    }

    $header = Stream::getContents($file, 4);
    $class  = null;

    switch ($header) {
      case "\x00\x01\x00\x00":
      case "true":
      case "typ1":
        $class = "TrueType\\File";
        break;

      case "OTTO":
        $class = "OpenType\\File";
        break;

      case "wOFF":
        $class = "WOFF\\File";
        break;

      case "ttcf":
        $class = "TrueType\\Collection";
        break;

      // Unknown type or EOT
      default:
        $magicNumber = Stream::getContents($file, 2, 34);

        if ($magicNumber === "LP") {
          $class = "EOT\\File";
        }
    }

    if ($class) {
      $class = "FontLib\\$class";

      /** @var TrueType\File|OpenType\File|WOFF\File|TrueType\Collection|EOT\File $obj */
      $obj = new $class;
      $obj->load($file);

      return $obj;
    }

    return null;
  }

  static function d(string $str) {
    if (!self::$debug) {
      return;
    }
    error_log("$str\n");
  }

  static function UTF16ToUTF8(string $str) {
    return Iconv
