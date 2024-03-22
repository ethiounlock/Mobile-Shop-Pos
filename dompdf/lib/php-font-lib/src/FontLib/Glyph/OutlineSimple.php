<?php

declare(strict_types=1);

namespace FontLib\Glyph;

use FontLib\Font;

/**
 * `glyf` font table.
 */
class OutlineSimple extends Outline
{
    public const ON_CURVE       = 0x01;
    public const X_SHORT_VECTOR = 0x02;
    public const Y_SHORT_VECTOR = 0x04;
    public const REPEAT         = 0x08;
    public const THIS_X_IS_SAME = 0x10;
    public const THIS_Y_IS_SAME = 0x20;

    /** @var array */
    public $points;

    /**
     * @param int $numberOfContours
     */
    public function parseData(int $numberOfContours): void
    {
        parent::parseData();

        if (!$this->size) {
            return;
        }

        $font = $this->getFont();

        // ... rest of the method code ...
    }

    // ... rest of the class methods ...

    /**
     * @param array $points
     * @return int
     */
    public function encodePoints(array $points): int
    {
        // ... rest of the method code ...
    }

    /**
     * @param array $points
     * @return string
     */
    public function getSVGContours(array $points = null): string
    {
        // ... rest of the method code ...
    }

    /**
     * @param array $points
     * @param int $startIndex
     * @param int $count
     * @return string
     */
    protected function getSVGPath(array $points, int $startIndex, int $count): string
    {
        // ... rest of the method code ...
    }

    /**
     * @param float $a
     * @param float $b
     * @return float
     */
    protected function midValue(float $a, float $b): float
    {
        // ... rest of the method code ...
    }
}
