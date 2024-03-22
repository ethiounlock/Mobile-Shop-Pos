<?php

declare(strict_types=1);

namespace Svg\Surface;

use Svg\Style;

const FILL_RULE_NONZERO = 'nonzero';
const FILL_RULE_EVENODD = 'evenodd';

/**
 * Interface SurfaceInterface, like CanvasRenderingContext2D
 *
 * @package Svg
 */
interface SurfaceInterface
{
    public function save();

    public function restore();

    // transformations (default transform is the identity matrix)
    public function scale(float $x, float $y): void;

    public function rotate(float $angle): void;

    public function translate(float $x, float $y): void;

    public function transform(
        float $a,
        float $b,
        float $c,
        float $d,
        float $e,
        float $f
    ): void;

    // path ends
    public function beginPath(): void;

    public function closePath(): void;

    public function fill(string $fillRule = FILL_RULE_NONZERO): void;

    public function stroke(): void;

    public function endPath(): void;

    public function fillStroke(): void;

    public function clip(): void;

    // text (see also the CanvasDrawingStyles interface)
    public function fillText(
        string $text,
        float $x,
        float $y,
        ?float $maxWidth = null
    ): void;

    public function strokeText(
        string $text,
        float $x,
        float $y,
        ?float $maxWidth = null
    ): void;

    public function measureText(string $text): array;

    // drawing images
    public function drawImage(
        $image,
        float $sx,
        float $sy,
        ?float $sw = null,
        ?float $sh = null,
        float $dx,
        float $dy,
        ?float $dw = null,
        ?float $dh = null
    ): void;

    // paths
    public function lineTo(float $x, float $y): void;

    public function moveTo(float $x, float $y): void;

    public function quadraticCurveTo(float $cpx, float $cpy, float $x, float $y): void;

    public function bezierCurveTo(
        float $cp1x,
        float $cp1y,
        float $cp2x,
        float $cp2y,
        float $x,
        float $y
    ): void;

    public function arcTo(
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        float $radius
    ): void;

    public function circle(float $x, float $y, float $radius): void;

    public function arc(
        float $x,
        float $y,
        float $
