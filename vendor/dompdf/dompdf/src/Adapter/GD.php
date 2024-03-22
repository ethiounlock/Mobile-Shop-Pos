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
class GD implements Canvas
{
    /**
     * @var Dompdf
     */
    protected $dompdf;

    /**
     * @var resource
     */
    protected $img;

    /**
     * @var resource[]
     */
    protected $imgs;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $actualWidth;

    /**
     * @var int
     */
    protected $actualHeight;

    /**
     * @var int
     */
    protected $pageNumber;

    /**
     * @var int
     */
    protected $pageCount;

    /**
     * @var float
     */
    protected $aaFactor;

    /**
     * @var array
     */
    protected $colors;

    /**
     * @var int
     */
    protected $bgColor;

    /**
     * @var int
     */
    protected $dpi;

    /**
     * Class constructor
     *
     * @param array $size The size of image to create: array(x1,y1,x2,y2) or "letter", "legal", etc.
     * @param string $orientation The orientation of the document (either 'landscape' or 'portrait')
     * @param Dompdf $dompdf
     * @param float $aaFactor Anti-aliasing factor, 1 for no AA
     * @param array $bgColor Image background color: array(r,g,b,a), 0 <= r,g,b,a <= 1
     */
    public function __construct(
        array $size = ['letter'],
        string $orientation = 'portrait',
        Dompdf $dompdf = null,
        float $aaFactor = 1.0,
        array $bgColor = [1, 1, 1, 0]
    ) {
        $this->dpi = $this->getDompdf()->getOptions()->getDpi();

        if ($aaFactor < 1) {
            $aaFactor = 1;
        }

        $this->aaFactor = $aaFactor;

        if (is_array($size)) {
            $size = $this->normalizeSize($size);
        } else {
            $size = $this->getPaperSize($size);
        }

        if (strtolower($orientation) === 'landscape') {
            list($size[2], $size[3]) = [$size[3], $size[2]];
        }

        $this->width = $size[2] - $size[0];
        $this->height = $size[3] - $size[1];

        $this->actualWidth = $this->upscale($this->width);
        $this->actualHeight = $this->upscale($this->height);

        $this->pageNumber = $this->pageCount = 1;
        $this->pageText = [];

        $this->bgColor = $this->allocateColor($bgColor);

        $this->newPage();
    }

    /**
     * @return Dompdf
     */
    public function getDompdf(): Dompdf
    {
        return $this->dompdf;
    }

    /**
     * @param array $bgColor
     * @return int
     */
    protected function allocateColor(array $bgColor): int
    {
        $r = $bgColor[0] * 255;
        $g = $bgColor[1] * 255;
        $b = $bgColor[2] * 255;
        $a = (127 - ($bgColor[3] * 127)) & 0xFF;

        $key = sprintf('#%02X%02X%02X%02X', $r, $g, $b, $a);

        if (isset($this->colors[$key])) {
            return $this->colors[$key];
        }

        $color = imagecolorallocatealpha(
            $this->img,
            $r,
            $g,
            $b,
            $a
        );

        $this->colors[$key] = $color;

        return $color;
    }

    /**
     * @param array $size
     * @return array
     */
    protected function normalizeSize(array $size): array
    {
        return [
            $size[0] * $this->aaFactor,
            $size[1] * $this->aaFactor,
            $size[2] * $this->aaFactor,
            $size[3] * $this->aaFactor,
        ];
    }

    /**
     * @param string $size
     * @return array
     */
    protected function getPaperSize(string $size): array
    {
        if (!isset(CPDF::$PAPER_SIZES[$size])) {
            $size = CPDF::$PAPER_SIZES['letter'];
        }

        return $this->normalizeSize(CPDF::$PAPER_SIZES[$size]);
    }

    /**
     * @param float $length
     * @return float
     */
    protected function upscale(float $length): float
    {
        return
