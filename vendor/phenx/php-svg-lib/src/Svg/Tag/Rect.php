<?php

namespace Svg\Tag;

/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

class Rect extends Shape
{
    protected float $x = 0;
    protected float $y = 0;
    protected float $width = 0;
    protected float $height = 0;
    protected float $rx = 0;
    protected float $ry = 0;

    public function start(array $attributes): void
    {
        $this->x = $attributes['x'] ?? 0;
        $this->y = $attributes['y'] ?? 0;

        $this->width = $attributes['width'] ?? 0;
        if (substr($this->width, -1) === '%') {
            $factor = substr($this->width, 0, -1) / 100;
            $this->width = $this->document->getWidth() * $factor;
        }

        $this->height = $attributes['height'] ?? 0;
        if (substr($this->height, -1) === '%') {
            $factor = substr($this->height, 0, -1) / 100;
            $this->height = $this->document->getHeight() * $factor;
        }

        $this->rx = $attributes['rx'] ?? 0;
        $this->ry = $attributes['ry'] ?? 0;

        $this->document->getSurface()->rect(
            $this->x,
            $this->y,
            $this->width,
            $this->height,
            $this->rx,
            $this->ry
        );
    }
}
