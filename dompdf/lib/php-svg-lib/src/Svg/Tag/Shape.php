<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Tag;

use Svg\Style;

class Shape extends AbstractTag
{
    /** @var bool */
    protected bool $hasShape = false;

    /**
     * @param array $attributes
     */
    protected function before(array $attributes): void
    {
        $document = $this->document;
        if ($document === null) {
            throw new \RuntimeException('Document is not set.');
        }

        $surface = $document->getSurface();
        if ($surface === null) {
            throw new \RuntimeException('Surface is not set.');
        }

        $surface->save();

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
        $document = $this->document;
        if ($document === null) {
            throw new \RuntimeException('Document is not set.');
        }

        $surface = $document->getSurface();
        if ($surface === null) {
            throw new \RuntimeException('Surface is not set.');
        }

        if ($this->hasShape) {
            $style = $surface->getStyle();

            $fill = $style->fill && is_array($style->fill);
            $stroke = $style->stroke && is_array($style->stroke);

            if ($fill) {
                if ($stroke) {
                    $surface->fillStroke();
                } else {
                    if (is_string($style->fill)) {
                        /** @var LinearGradient|RadialGradient $gradient */
                        $gradient = $this->getDocument()->get
