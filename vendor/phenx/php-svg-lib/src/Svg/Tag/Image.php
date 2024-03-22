<?php

namespace Svg\Tag;

/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

class Image extends AbstractTag
{
    /**
     * @var int
     */
    protected $x = 0;

    /**
     * @var int
     */
    protected $y = 0;

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var string|null
     */
    protected $href = null;

    /**
     * @param array $attributes
     */
    protected function before(array $attributes): void
    {
        parent::before($attributes);

        $surface = $this->document->getSurface();
        $surface->save();

        $this->applyTransform($attributes);
    }

    /**
     * @param array $attributes
     * @throws \Exception
     */
    public function start(array $attributes): void
    {
        $document = $this->document;
        $height = $document->getHeight();
        $this->y = $height;

        $this->x = isset($attributes['x']) ? (int)$attributes['x'] : 0;
        $this->y = $height - (isset($attributes['y']) ? (int)$attributes['y'] : 0);

        $this->width = isset($attributes['width']) ? (int)$attributes['width'] : 0;
        if ($this->width <= 0) {
            throw new \Exception('Invalid image width.');
        }

        $this->height = isset($attributes['height']) ? (int)$attributes['height'] : 0;
        if ($this->height <= 0) {
            throw new \Exception('Invalid image height.');
        }

        $this->href = isset($attributes
