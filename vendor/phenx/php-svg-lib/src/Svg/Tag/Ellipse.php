<?php

namespace Svg\Tag;

use Svg\Document;

/**
 * Class Ellipse
 * @package Svg\Tag
 *
 * @author Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class Ellipse extends Shape
{
    protected int $cx = 0;
    protected int $cy = 0;
    protected int $rx = 0;
    protected int $ry = 0;

    /**
     * @param array $attributes
     */
    public function start(array $attributes): void
    {
        if (!isset($attributes['cx']) || !is_numeric($attributes['cx'])) {
            throw new \InvalidArgumentException('Missing or invalid "cx" attribute.');
        }
        $this->cx = (int) ($attributes['cx'] ?? 0);

        if (!isset($attributes['cy']) || !is_numeric($attributes['cy'])) {
            throw new \InvalidArgumentException('Missing or invalid "cy" attribute.');
        }
        $this->cy = (int) ($attributes['cy'] ?? 0);

        if (!isset($attributes['rx']) || !is_numeric($attributes['rx'])) {
            throw new \InvalidArgumentException('Missing or invalid "rx" attribute.');
        }
        $this->rx = (int) ($attributes['rx'] ?? 0);

        if (!isset($attributes['ry']) || !is_numeric($attributes['ry'])) {
            throw new \InvalidArgumentException('Missing or invalid "ry" attribute.');
        }
        $this->ry = (int) ($attributes['ry'] ?? 0);


