<?php

declare(strict_types=1);

namespace Svg\Tag;

use Svg\Document;
use Svg\Surface;

/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class UseTag extends AbstractTag
{
    protected ?int $x = 0;
    protected ?int $y = 0;
    protected ?int $width;
    protected ?int $height;

    /** @var AbstractTag|null */
    protected ?AbstractTag $reference = null;

    protected function before(array $attributes): void
    {
        if (isset($attributes['x'])) {
            $this->x = (int)$attributes['x'];
        }
        if (isset($attributes['y'])) {
            $this->y = (int)$attributes['y'];
        }

        if (isset($attributes['width'])) {
            $this->width = (int)$attributes['width'];
        }
        if (isset($attributes['height'])) {
            $this->height = (int)$attributes['height'];
        }

        $document = $this->getDocument();
        if (!$document instanceof Document) {
            throw new \RuntimeException('Document instance is not valid.');
        }

        $link = $attributes["xlink:href"] ?? '';
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL provided.');
        }

        $this->reference = $document->getDef($link);

        if ($this->reference) {
            $this->reference->before($attributes);
        }

        $surface = $document->getSurface();
        if (!$surface instanceof Surface) {
            throw new \RuntimeException('Surface instance is not valid.');
        }

        $surface->save();
        $surface->translate($this->x, $this->y);
    }

    protected function after(): void
    {
        if ($this->reference) {
            $this->reference->after();
        }

        $this->getDocument()->getSurface()->restore();
    }

    public function handle(array $attributes): void
    {
        if (!$this->reference) {
            return;
        }

        $attributes = array_merge($this->reference->attributes, $attributes);

        $this->reference->handle($attributes);

        foreach ($this->reference->children as $_child) {
            $_attributes = array_merge($_child->attributes, $attributes);
            $_child->handle($_attributes);
        }
    }

    public function handleEnd(): void
    {
        if (!$this->reference) {
            return;
        }

        $this->reference->handleEnd();

        foreach ($this->reference->children as $_child) {
            $_child->handleEnd();

