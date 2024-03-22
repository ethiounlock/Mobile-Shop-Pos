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
    protected function _background_image(
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

        $box_width = $width;
        $box_height = $height;

        //debugpng
        if ($this->_dompdf->getOptions()->getDebugPng()) {
            print '[_background_image ' . $url . ']';
        }

        list($img, $type, /*$msg*/) = Cache::resolve_url(
            $url,
            $sheet->get_protocol(),
            $sheet->get_host(),
            $sheet->get_base_path(),
            $this->_dompdf
        );

        // Bail if the image is no good
        if (Cache::is_broken($img)) {
            return;
        }

        //Try to optimize away reading and composing of same background multiple times
        //Postponing read with imagecreatefrom   ...()
        //final composition parameters and name not known yet
        //Therefore read dimension directly from file, instead of creating gd object first.
        //$img_w = imagesx($src); $img_h = imagesy($src);

        list($img_w, $img_h) = Helpers::dompdf_getimagesize($img, $this->_dompdf->getHttpContext());
        if (!isset($img_w) || $img_w == 0 || !isset($img_h) || $img_h == 0) {
            return;
        }

        // save for later check if file needs to be resized.
        $org_img_w = $img_w;
        $org_img_h = $img_h;

        $repeat = $style->background_repeat;
        $dpi = $this->_dompdf->getOptions()->getDpi();

        //Increase background resolution and dependent box size according to image resolution to be placed in
        //Then image can be copied in without resize
        $bg_width = round((float)($width * $dpi) / 72);
        $bg_height = round((float)($height * $dpi) / 72);

        list($img_w, $img_h) = $this->_resize_background_image(
            $img_w,
            $img_h,
            $bg_width,
            $bg_height,
            $style->background_size,
            $dpi
        );
        //Need %bg_x, $bg_y as background pos, where img starts, converted to pixel

        list($bg_x, $bg_y) = $style->background_position;

        if (Helpers::is_percent($bg_x)) {
            // The point $bg_x % from the left edge of the image is placed
            // $bg_x % from the left edge of the background rectangle
            $p = ((float)$bg_x) / 100.0;
            $x1 = $p * $img_w;
            $x2 = $p * $bg_width;

            $bg_x = $x2 - $x1;
        } else {
            $bg_x = (float)($style->length_in_pt($bg_x) * $dpi) / 72;
        }

        $bg_x = round($bg_x + (float)$style->length_in_pt($style->border_left_width) * $dpi / 72);

        if (Helpers::is_percent($bg_y)) {
            // The point $bg_y % from the left edge of the image is placed
            // $bg_y % from the left edge of the background rectangle
            $p = ((float)$bg_y) / 100.0;
