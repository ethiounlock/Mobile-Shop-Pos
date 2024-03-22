<?php

declare(strict_types=1);

namespace Dompdf\Renderer;

use Dompdf\Adapter\CPDF;
use Dompdf\Css\Color;
use Dompdf\Css\Style;
use Dompdf\Dompdf;
use Dompdf\Helpers;
use Dompdf\Frame;
use Dompdf\Image\Cache;

/**
 * Base renderer class
 *
 * @package dompdf
 */
abstract class AbstractRenderer
{

    /**
     * @var \Dompdf\Canvas
     */
    protected $_canvas;

    /**
     * @var Dompdf
     */
    protected $_dompdf;

    /**
     * Class constructor
     *
     * @param Dompdf $dompdf The current dompdf instance
     */
    public function __construct(Dompdf $dompdf)
    {
        $this->_dompdf = $dompdf;
        $this->_canvas = $dompdf->getCanvas();
    }

    /**
     * Render a frame.
     *
     * Specialized in child classes
     *
     * @param Frame $frame The frame to render
     */
    abstract public function render(Frame $frame): void;

    /**
     * Render a background image over a rectangular area
     *
     * @param string $url   The background image to load
     * @param float $x      The left edge of the rectangular area
     * @param float $y      The top edge of the rectangular area
     * @param float $width  The width of the rectangular area
     * @param float $height The height of the rectangular area
     * @param Style $style  The associated Style object
     *
     * @throws \Exception
     */
    protected function _backgroundImage(
        string $url,
        float $x,
        float $y,
        float $width,
        float $height,
        Style $style
    ): void {
        if (!function_exists("imagecreatetruecolor")) {
            throw new \Exception("The PHP GD extension is required, but is not installed.");
        }

        $sheet = $style->get_stylesheet();

        // Skip degenerate cases
        if ($width == 0 || $height == 0) {
            return;
        }

        $boxWidth = $width;
        $boxHeight = $height;

        //debugpng
        if ($this->_dompdf->getOptions()->getDebugPng()) {
            print '[_background_image ' . $url . ']';
        }

        [$img, $type, /*$msg*/] = Cache::resolveUrl(
            $url,
            $sheet->get_protocol(),
            $sheet->get_host(),
            $sheet->get_base_path(),
            $this->_dompdf
        );

        // Bail if the image is no good
        if (Cache::isBroken($img)) {
            return;
        }

        //Try to optimize away reading and composing of same background multiple times
        //Postponing read with imagecreatefrom   ...()
        //final composition parameters and name not known yet
        //Therefore read dimension directly from file, instead of creating gd object first.
        //$img_w = imagesx($src); $img_h = imagesy($src);

        [$imgWidth, $imgHeight] = Helpers::dompdfGetimagesize($img, $this->_dompdf->getHttpContext());
        if (!isset($imgWidth) || $imgWidth == 0 || !isset($imgHeight) || $imgHeight == 0) {
            return;
        }

        // save for later check if file needs to be resized.
        $orgImgWidth = $imgWidth;
        $orgImgHeight = $imgHeight;

        $repeat = $style->backgroundRepeat;
        $dpi = $this->_dompdf->getOptions()->getDpi();

        //Increase background resolution and dependent box size according to image resolution to be placed in
        //Then image can be copied in without resize
        $bgWidth = round((float)($width * $dpi) / 72);
        $bgHeight = round((float)($height * $dpi) / 72);

        [$imgWidth, $imgHeight] = $this->_resizeBackgroundImage(
            $imgWidth,
            $imgHeight,
            $bgWidth,
            $bgHeight,
            $style->backgroundSize,
            $dpi
        );
        //Need %bg_x, $bg_y as background pos, where img starts, converted to pixel

        [$bgX, $bgY] = $style->backgroundPosition;

        if (Helpers::isPercent($bgX)) {
            // The point $bg_x % from the left edge of the image is placed
            // $bg_x % from the left edge of the background rectangle
            $p = ((float)$bgX) / 100.0;
            $x1 = $p * $imgWidth;
            $x2 = $p * $bgWidth;

            $bgX = $x2 - $x1;
        } else {
            $bgX = (float)($style->lengthInPt($bgX) * $dpi) / 72;
        }

        $bgX = round($bgX + (float)$style->lengthInPt($style->borderLeftWidth) * $dpi / 72);

        if (Helpers::isPercent($bgY)) {
            // The point $bg_y % from the left edge of the image is placed
            // $bg_y % from the left edge of the background rectangle
            $p = ((float)$bgY) / 100.0;
            $y1 = $p * $imgHeight;
            $y2 = $p * $bgHeight;

           
