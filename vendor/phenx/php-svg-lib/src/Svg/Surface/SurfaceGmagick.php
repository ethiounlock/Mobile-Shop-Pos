<?php

declare(strict_types=1);

namespace Svg\Surface;

use Gmagick;
use GmagickDraw;
use GmagickPixel;
use InvalidArgumentException;
use RuntimeException;
use Svg\Style;

/**
 * Class SurfaceGmagick
 * @package Svg\Surface
 *
 * @author Fabien Ménager <fabien.menager@gmail.com>
 * @link   http://github.com/PhenX/php-svg-lib
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class SurfaceGmagick implements SurfaceInterface
{
    const DEBUG = false;

    /** @var GmagickDraw */
    private $canvas;

    private int $width;
    private int $height;

    /** @var Style */
    private Style $style;

    /**
     * SurfaceGmagick constructor.
     * @param int $w
     * @param int $h
     */
    public function __construct(int $w, int $h)
    {
        if (self::DEBUG) {
            echo __FUNCTION__ . "\n";
        }
        $this->width = $w;
        $this->height = $h;

        $canvas = new GmagickDraw();

        $this->canvas = $canvas;
    }

    /**
     * @return string
     */
    public function out(): string
    {
        if (self::DEBUG) {
            echo __FUNCTION__ . "\n";
        }

        $image = new Gmagick();
        $image->newimage($this->width, $this->height);
        $image->drawimage($this->canvas);

        $tmp = tempnam("", "gm");

        $image->write($tmp);

        return file_get_contents($tmp);
    }

    /**
     * Saves the current state of the canvas.
     */
    public function save(): void
    {
        if (self::DEBUG) {
            echo __FUNCTION__ . "\n";
        }
        $this->canvas->save();
    }

    /**
     * Restores the canvas to the last saved state.
     */
    public function restore(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->restore();
    }

    /**
     * Scales the canvas by the given factors.
     * @param float $x
     * @param float $y
     */
    public function scale(float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->scale($x, $y);
    }

    /**
     * Rotates the canvas by the given angle.
     * @param float $angle
     */
    public function rotate(float $angle): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->rotate($angle);
    }

    /**
     * Translates the canvas by the given amount.
     * @param float $x
     * @param float $y
     */
    public function translate(float $x, float $y): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->translate($x, $y);
    }

    /**
     * Transforms the canvas using the given matrix.
     * @param float $a
     * @param float $b
     * @param float $c
     * @param float $d
     * @param float $e
     * @param float $f
     */
    public function transform(float $a, float $b, float $c, float $d, float $e, float $f): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->concat($a, $b, $c, $d, $e, $f);
    }

    /**
     * Begins a new path.
     */
    public function beginPath(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        // TODO: Implement beginPath() method.
    }

    /**
     * Closes the current path.
     */
    public function closePath(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->closepath();
    }

    /**
     * Fills and strokes the current path.
     */
    public function fillStroke(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->fill_stroke();
    }

    /**
     * Clips the canvas to the current path.
     */
    public function clip(): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->clip();
    }

    /**
     * Fills the given text at the specified position.
     * @param string $text
     * @param float $x
     * @param float $y
     * @param float|null $maxWidth
     */
    public function fillText(string $text, float $x, float $y, ?float $maxWidth = null): void
    {
        if (self::DEBUG) echo __FUNCTION__ . "\n";
        $this->canvas->set_text_pos($x, $y);
        $this->canvas->show($text);
    }

    /**
     * Strokes the given text at the specified position.
     * @param string $text
     * @param float $x
     * @param float $y
