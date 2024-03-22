<?php

namespace Svg\Tag;

use Svg\Style;
use Svg\Surface;

class Group extends AbstractTag
{
    /**
     * @var Surface
     */
    private $surface;

    /**
     * @var Style
     */
    private $style;

    /**
     * Group constructor.
     * @param Surface $surface
     */
    public function __construct(Surface $surface)
    {
        $this->surface = $surface;
    }

    protected function before(array $attributes): void
    {
        $this->surface->save();

        try {
            $this->style = $this->makeStyle($attributes);
            $this->setStyle($this->style);
            $this->surface->setStyle($this->style);
            $this->applyTransform($attributes);
        } catch (\Exception $e) {
            // Log or handle the exception here
            // Don't forget to restore the surface state in case of failure
            $this->surface->restore();
            throw $e;
        }
    }

    protected function after(): void
    {
        $this->surface->restore();
    }
}
