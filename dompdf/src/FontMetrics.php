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
    
