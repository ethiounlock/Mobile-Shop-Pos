<?php

namespace Svg\Tag;

use Svg\Style;

class ClipPath extends AbstractTag
{
    /**
     * @param array|null $attributes
     */
    protected function before(?array $attributes): void
    {
        $surface = $this->document->getSurface();

        if (!is_resource($surface)) {
            throw new \RuntimeException('Surface resource is not valid.');
        }

        $surfaceSaveResult = $surface->save();

        if ($surfaceSaveResult === false) {
            throw new \RuntimeException('Failed to save surface state.');
        }

        $style = $this->makeStyle($attributes);

        $this->setStyle($style);
        $surface->setStyle($style);

        $this->applyTransform($attributes);
    }

    /**
     * @return void
     */
    protected function after(): void
    {
        $surface = $this->document->getSurface();

        if (!is_resource($surface)) {
            throw new \RuntimeException('Surface resource is not valid.');
        }

        $surfaceRestoreResult = $surface->restore();

        if ($surfaceRestoreResult === false) {
            throw new \RuntimeException('Failed to restore surface state.');
        }
    }
}
