<?php

namespace Svg\Surface;

use Svg\Style;

class SurfaceGmagick implements SurfaceInterface
{
    const DEBUG = false;

    /** @var \GmagickDraw */
    private $canvas;

    private int $width;
    private int $height;

    /** @var Style */
    private Style $style;

    public function __construct(int $w, int $h)
    {
        if (self::DEBUG) {
            echo __FUNCTION__ . "\n";
        }
        $this->width = $w;
        $this->height = $h;

        $canvas = new \GmagickDraw();

        $this->canvas = $canvas;
    }

    public function out(): string
    {
        if (self::DEBUG) {
            echo __FUNCTION__ . "\n";
        }

        $image = new \Gmagick();
        $image->newimage($this->width, $this->height);
        $image->drawimage($this->canvas);

        $tmp = tempnam("", "gm");

        $image->write($tmp);

        return file_get_contents($tmp);
    }

    public function save(): void
    {
        if (self::DEBUG) {
            echo __FUNCTION__ . "\n";
        }
        $this->canvas->save();
    }

    public function restore(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->restore();
    }

    public function scale(float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->scale($x, $y);
    }

    public function rotate(float $angle): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->rotate($angle);
    }

    public function translate(float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->translate($x, $y);
    }

    public function transform(float $a, float $b, float $c, float $d, float $e, float $f): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->concat($a, $b, $c, $d, $e, $f);
    }

    public function beginPath(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->beginpath();
    }

    public function closePath(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->closepath();
    }

    public function fillStroke(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->fill_stroke();
    }

    public function clip(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->clip();
    }

    public function fillText(string $text, float $x, float $y, float $maxWidth = null): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->set_text_pos($x, $y);
        $this->canvas->show($text);
    }

    public function strokeText(string $text, float $x, float $y, float $maxWidth = null): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        // TODO: Implement drawImage() method.
    }

    public function drawImage(string $image, float $sx, float $sy, float $sw = null, float $sh = null, float $dx = null, float $dy = null, float $dw = null, float $dh = null): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";

        if (strpos($image, "data:") === 0) {
            $data = substr($image, strpos($image, ";") + 1);
            if (strpos($data, "base64") === 0) {
                $data = base64_decode(substr($data, 7));
            }

            $image = tempnam("", "svg");
            file_put_contents($image, $data);
        }

        $img = $this->canvas->load_image("auto", $image, "");

        $sy = $sy - $sh;
        $this->canvas->fit_image($img, $sx, $sy, 'boxsize={' . "$sw $sh" . '} fitmethod=entire');
    }

    public function lineTo(float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->lineto($x, $y);
    }

    public function moveTo(float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->moveto($x, $y);
    }

    public function quadraticCurveTo(float $cpx, float $cpy, float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        // TODO: Implement quadraticCurveTo() method.
    }

    public
