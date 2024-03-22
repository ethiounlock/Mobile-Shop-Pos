<?php

declare(strict_types=1);

namespace Dompdf\Image;

use Dompdf\DOMpdf;
use Dompdf\Helpers;
use Dompdf\Exception\ImageException;

/**
 * Static class that resolves image urls and downloads and caches
 * remote images if required.
 *
 * @package dompdf
 */
class Cache
{
    /**
     * Array of downloaded images.  Cached so that identical images are
     * not needlessly downloaded.
     *
     * @var array
     */
    protected static $_cache = [];

    /**
     * The url to the "broken image" used when images can't be loaded
     *
     * @var string
     */
    public static $broken_image = "data:image/svg+xml;charset=utf8,%3C%3Fxml version='1.0'?%3E%3Csvg width='64' height='64' xmlns='http://www.w3.org/2000/svg'%3E%3Cg%3E%3Crect stroke='%23666666' id='svg_1' height='60.499994' width='60.166667' y='1.666669' x='1.999998' stroke-width='1.5' fill='none'/%3E%3Cline stroke-linecap='null' stroke-linejoin='null' id='svg_3' y2='59.333253' x2='59.749916' y1='4.333415' x1='4.250079' stroke-width='1.5' stroke='%23999999' fill='none'/%3E%3Cline stroke-linecap='null' stroke-linejoin='null' id='svg_4' y2='59.999665' x2='4.062838' y1='3.750342' x1='60.062164' stroke-width='1.5' stroke='%23999999' fill='none'/%3E%3C/g%3E%3C/svg%3E";

    public static $error_message = "Image not found or type unknown";
    
    /**
     * Current dompdf instance
     *
     * @var DOMpdf
     */
    protected static $_dompdf;

    /**
     * Resolve and fetch an image for use.
     *
     * @param string $url       The url of the image
     * @param string $protocol  Default protocol if none specified in $url
     * @param string $host      Default host if none specified in $url
     * @param string $base_path Default path if none specified in $url
     * @param DOMpdf $dompdf    The Dompdf instance
     *
     * @throws ImageException
     * @return array             An array with two elements: The local path to the image and the image extension
     */
    static function resolve_url(string $url, DOMpdf $dompdf): array
    {
        self::$_dompdf = $dompdf;

        $parsed_url = Helpers::explode_url($url);
        $message = null;

        $remote = ($parsed_url['protocol'] != "");

        $data_uri = strpos($parsed_url['protocol'], "data:") === 0;
        $full_url = null;
        $enable_remote = $dompdf->getOptions()->getIsRemoteEnabled();

        try {

            // Remote not allowed and is not DataURI
            if (!$enable_remote && $remote && !$data_uri) {
                throw new ImageException("Remote file access is disabled.", E_WARNING);
            }
            
            // remote allowed or DataURI
            if (($enable_remote && $remote) || $data_uri) {
                // Download remote files to a temporary directory
                $full_url = Helpers::build_url($url);

                // From cache
                if (isset(self::$_cache[$full_url])) {
                    $resolved_url = self::$_cache[$full_url];
                } // From remote
                else {
                    $tmp_dir = $dompdf->getOptions()->getTempDir();
                    if (($
