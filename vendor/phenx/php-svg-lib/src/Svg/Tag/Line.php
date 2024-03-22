<?php

namespace Svg\Tag;

/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

class Line extends Shape
{
    protected int $x1 = 0;
    protected int $y1 = 0;

    protected int $x2 = 0;
    protected int $y2 = 0;

    public function start(array $attributes): void
    {
        $this->x1 = $attributes['x1'] ?? 0;
        $this->y1 = $attributes['y1'] ?? 0;
        $this->x2 = $attributes['x2'] ?? 0;
        $this->y2 = $attributes['y2'] ?? 0;

        $surface = $this->document->getSurface();
        $surface->moveTo($this->x1, $this->y1);
        $surface->lineTo($this->x2, $this->y2);
    }
}
