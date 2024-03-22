<?php

namespace Dompdf;

use FontLib\Font;

/**
 * The font metrics class
 *
 * This class provides information about fonts and text.  It can resolve
 * font names into actual installed font files, as well as determine the
 * size of text in a particular font and size.
 *
 * @package dompdf
 */
class FontMetrics
{
    const CACHE_FILE = "dompdf_font_family_cache.php";

    /**
     * @var Canvas
     */
    private $canvas;

    /**
     * @var Options
     */
    private $options;

    /**
     * Array of font family names to font files
     *
     * Usually cached by the {@link load_font.php} script
     *
     * @var array
     */
    private $fontLookup = [];

    /**
     * Class initialization
     */
    public function __construct(Canvas $canvas, Options $options)
    {
        $this->setCanvas($canvas);
        $this->setOptions($options);
        $this->loadFontFamilies();
    }

    /**
     * Saves the stored font family cache
     *
     * The name and location of the cache file are determined by {@link
     * FontMetrics::CACHE_FILE}. This file should be writable by the
     * webserver process.
     *
     * @see FontMetrics::loadFontFamilies()
     */
    public function saveFontFamilies()
    {
        // replace the path to the DOMPDF font directories with the corresponding constants (allows for more portability)
        $cacheData = sprintf("<?php return array (%s", PHP_EOL);
        foreach ($this->fontLookup as $family => $variants) {
            $cacheData .= sprintf("  '%s' => array(%s", addslashes($family), PHP_EOL);
            foreach ($variants as $variant => $path) {
                $path = sprintf("'%s'", $path);
                $path = str_replace(
                    '\'' . $this->getOptions()->getFontDir() ,
                    '$fontDir . \'' ,
                    $path
                );
                $path = str_replace(
                    '\'' . $this->getOptions()->getRootDir() ,
                    '$rootDir . \'' ,
                    $path
                );
                $cacheData .= sprintf("    '%s' => %s,%s", $variant, $path, PHP_EOL);
            }
            $cacheData .= sprintf("  ),%s", PHP_EOL);
        }
        $cacheData .= ") ?>";
        file_put_contents($this->getCacheFile(), $cacheData);
    }

    /**
     * Loads the stored font family cache
     *
     * @see FontMetrics::saveFontFamilies()
     */
    public function loadFontFamilies()
    {
        $fontDir = $this->getOptions()->getFontDir();
        $rootDir = $this->getOptions()->getRootDir();

        // FIXME: temporarily define constants for cache files <= v0.6.2
        if (!defined("DOMPDF_DIR")) {
            define("DOMPDF_DIR", $rootDir);
        }
        if (!defined("DOMPDF_FONT_DIR")) {
            define("DOMPDF_FONT_DIR", $fontDir);
        }

        $file = $rootDir . "/lib/fonts/dompdf_font_family_cache.dist.php";
        $distFonts = require $file;

        if (!is_readable($this->getCacheFile())) {
            $this->fontLookup = $distFonts;
            return;
        }

        $cacheData = require $this->getCacheFile();

        $this->fontLookup = [];
        if (is_array($this->fontLookup)) {
            foreach ($cacheData as $key => $value) {
                $this->fontLookup[stripslashes($key)] = $value;
            }
        }

        // Merge provided fonts
        $this->fontLookup += $distFonts;
    }

    /**
     * @param array $style
     * @param string $remoteFile
     * @param resource $context
     * @return bool
     */
    public function registerFont($style, $remoteFile, $context = null)
    {
        $fontname = mb_strtolower($style["family"]);
        $families = $this->getFontFamilies();

        $entry = [];
        if (isset($families[$fontname])) {
            $entry = $families[$fontname];
        }

        $styleString = $this->getType("{$style['weight']} {$style['style']}");

        $fontDir = $this->getOptions()->getFontDir();
        $remoteHash = md5($remoteFile);

        $prefix = $fontname . "_" . $styleString;
        $prefix = trim($prefix, "-");
        if (function_exists('iconv')) {
            $prefix = @iconv('utf-8', 'us-ascii//TRANSLIT', $prefix);
        }
        $prefix_encoding = mb_detect_encoding($prefix, mb_detect_order(), true);
        $substchar = mb_substitute_character();
        mb_substitute_character(0x005F);
        $prefix = mb_convert_encoding($prefix, "ISO-8859-1", $prefix_encoding);
        mb_substitute_character($substchar);
        $prefix = preg_replace("[\W]", "_", $prefix);
        $prefix = preg_replace("/[^-_\w]+/", "", $prefix);

        $localFile = $fontDir . "/" . $prefix . "_" . $remoteHash;

        if (isset($entry[$styleString]) && $localFile == $entry[$styleString]) {
            return true;
        }

        $cacheEntry = $localFile;
        $localFile .= "." . strtolower(pathinfo(parse_url($remoteFile
