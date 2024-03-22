<?php

declare(strict_types=1);

namespace Dompdf\Adapter;

use Dompdf\Canvas;
use Dompdf\Dompdf;
use Dompdf\Image\Cache;
use Dompdf\Helpers;

/**
 * Image rendering interface
 *
 * Renders to an image format supported by GD (jpeg, gif, png, xpm).
 * Not super-useful day-to-day but handy nonetheless
 *
 * @package dompdf
 */
class GD implements Canvas, \Dompdf\Image, \Dompdf\Resource, \Dompdf\FontMetrics
{
    /**
     * @var Dompdf
     */
    protected $_dompdf;

    /**
     * Resource handle for the image
     *
     * @var resource
     */
    protected $_img;

    /**
     * Resource handle for the image
     *
     * @var resource[]
     */
    protected $_imgs;

    /**
     * Apparent canvas width in pixels
     *
     * @var int
     */
    protected $_width;

    /**
     * Apparent canvas height in pixels
     *
     * @var int
     */
    protected $_height;

    /**
     * Actual image width in pixels
     *
     * @var int
     */
    protected $_actual_width;

    /**
     * Actual image height in pixels
     *
     * @var int
     */
    protected $_actual_height;

    /**
     * Current page number
     *
     * @var int
     */
    protected $_page_number;

    /**
     * Total number of pages
     *
     * @var int
     */
    protected $_page_count;

    /**
     * Image antialias factor
     *
     * @var float
     */
    protected $_aa_factor;

    /**
     * Allocated colors
     *
     * @var array
     */
    protected $_colors;

    /**
     * Background color
     *
     * @var int
     */
    protected $_bg_color;

    /**
     * Background color array
     *
     * @var int
     */
    protected $_bg_color_array;

    /**
     * Actual DPI
     *
     * @var int
     */
    protected $dpi;

    /**
     * Amount to scale font sizes
     *
     * Font sizes are 72 DPI, GD internally uses 96. Scale them proportionally.
     * 72 / 96 = 0.75.
     *
     * @var float
     */
    const FONT_SCALE = 0.75;

    /**
     * Class constructor
     *
     * @param array $size The size of image to create: array(x1,y1,x2,y2) or "letter", "legal", etc.
     * @param string $orientation The orientation of the document (either 'landscape' or 'portrait')
     * @param Dompdf $dompdf
     * @param float $aa_factor Anti-aliasing factor, 1 for no AA
     * @param array $bg_color Image background color: array(r,g,b,a), 0 <= r,g,b,a <= 1
     */
    public function __construct(
        array $size = ['letter'],
        string $orientation = "portrait",
        Dompdf $dompdf = null,
        float $aa_factor = 1.0,
        array $bg_color = [1, 1, 1, 0]
    ) {
        $this->dpi = $this->get_dompdf()->getOptions()->getDpi();

        if ($aa_factor < 1) {
            $aa_factor = 1;
        }

        $this->_aa_factor = $aa_factor;

        if (is_array($size)) {
            $this->_width = $size[2] - $size[0];
            $this->_height = $size[3] - $size[1];
        } else {
            $size = strtolower($size);

            if (isset(CPDF::$PAPER_SIZES[$size])) {
                $size = CPDF::$PAPER_SIZES[$size];
            } else {
                $size = CPDF::$PAPER_SIZES["letter"];
            }
        }

        if (strtolower($orientation) === "landscape") {
            list($size[2], $size[3]) = [$size[3], $size[2]];
        }

        if ($dompdf === null) {
            $this->_dompdf = new Dompdf();
        } else {
            $this->_dompdf = $dompdf;
        }

        $this->_actual_width = $this->_upscale($this->_width);
        $this->_actual_height = $this->_upscale($this->_height);

        $this->_page_number = $this->_page_count = 1;
        $this->_page_text = [];

        if (is_null($bg_color) || !is_array($bg_color)) {
            // Pure white bg
            $bg_color = [1, 1, 1, 0];
        }

        $this->_bg_color_array = $bg_color;

        $this->new_page();
    }

    // ... rest of the class methods
}
