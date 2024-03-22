<?php

namespace Svg\Surface;

use CPdf\CPdf;
use Svg\Style;

class SurfaceCpdf implements SurfaceInterface
{
    const DEBUG = false;

    /** @var \CPdf\CPdf */
    private $canvas;

    private $width;
    private $height;

    /** @var Style */
    private $style;

    public function __construct(Document $doc, $canvas = null)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        $dimensions = $doc->getDimensions();
        $w = $dimensions["width"];
        $h = $dimensions["height"];

        if (!$canvas) {
            $canvas = new CPdf(array(0, 0, $w, $h));
            $refl = new \ReflectionClass($canvas);
            $canvas->fontcache = realpath(dirname($refl->getFileName()) . "/../../fonts/")."/";
        }

        // Flip PDF coordinate system so that the origin is in
        // the top left rather than the bottom left
        $canvas->transform(array(
            1,  0,
            0, -1,
            0, $h
        ));

        $this->width  = $w;
        $this->height = $h;

        $this->canvas = $canvas;
    }

    function out()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        return $this->canvas->output();
    }

    public function save()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->save();
    }

    public function restore()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->restore();
    }

    public function scale($x, $y)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        $this->transform($x, 0, 0, $y, 0, 0);
    }

    public function rotate($angle)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        $a = deg2rad($angle);
        $cos_a = cos($a);
        $sin_a = sin($a);

        $this->transform(
            $cos_a,                         $sin_a,
            -$sin_a,                         $cos_a,
            0, 0
        );
    }

    public function translate($x, $y)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        $this->transform(
            1,  0,
            0,  1,
            $x, $y
        );
    }

    public function transform($a, $b, $c, $d, $e, $f)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        $this->canvas->transform(array($a, $b, $c, $d, $e, $f));
    }

    public function beginPath()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        // TODO: Implement beginPath() method.
    }

    public function closePath()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->closePath();
    }

    public function fillStroke()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->fillStroke();
    }

    public function clip()
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->clip();
    }

    public function fillText($text, $x, $y, $maxWidth = null)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->addText($x, $y, $this->style->fontSize, $text);
    }

    public function strokeText($text, $x, $y, $maxWidth = null)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->addText($x, $y, $this->style->fontSize, $text);
    }

    public function drawImage($image, $sx, $sy, $sw = null, $sh = null, $dx = null, $dy = null, $dw = null, $dh = null)
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        if (strpos($image, "data:") === 0) {
            $parts = explode(',', $image, 2);

            $data = $parts[1];
            $base64 = false;

            $token = strtok($parts[0], ';');
            while ($token !== false) {
                if ($token == 'base64') {
                    $base64 = true;
                }

                $token = strtok(';');
            }

            if
