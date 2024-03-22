<?php

namespace Dompdf;

use Dompdf\Renderer\AbstractRenderer;
use Dompdf\Renderer\Block;
use Dompdf\Renderer\Image;
use Dompdf\Renderer\ListBullet;
use Dompdf\Renderer\TableCell;
use Dompdf\Renderer\TableRowGroup;
use Dompdf\Renderer\Text;

/**
 * Concrete renderer
 *
 * Instantiates several specific renderers in order to render any given frame.
 *
 * @package dompdf
 */
class Renderer extends AbstractRenderer
{

    /**
     * Array of renderers for specific frame types
     *
     * @var AbstractRenderer[]
     */
    protected array $_renderers;

    /**
     * Cache of the callbacks array
     *
     * @var array
     */
    private array $_callbacks;

    /**
     * Advance the canvas to the next page
     */
    public function newPage(): void
    {
        $this->_canvas->newPage();
    }

    /**
     * Render frames recursively
     *
     * @param Frame $frame the frame to render
     */
    public function render(Frame $frame): void
    {
        $this->_checkCallbacks('begin_frame', $frame);

        if ($_dompdf_debug) {
            error_log($frame);
        }

        $style = $frame->getStyle();

        if (in_array($style->visibility, ['hidden', 'collapse'])) {
            return;
        }

        $display = $style->display;

        // Starts the CSS transformation
        if ($style->transform && is_array($style->transform)) {
            $this->_canvas->save();
            [$x, $y] = $frame->getPaddingBox();
            $origin = $style->transformOrigin;

            foreach ($style->transform as [$function, $values]) {
                if ($function === 'matrix') {
                    $function = 'transform';
                }

                $values = array_map('floatval', $values);
                $values[] = $x + (float)$style->lengthInPt($origin[0], (float)$style->lengthInPt($style->width));
                $values[] = $y + (float)$style->lengthInPt($origin[1], (float)$style->lengthInPt($style->height));

                $this->_canvas->$function(...$values);
            }
        }

        switch ($display) {

            case 'block':
            case 'list-item':
            case 'inline-block':
            case 'table':
            case 'inline-table':
                $this->_renderFrame('block', $frame);
                break;

            case 'inline':
                if ($frame->isTextNode()) {
                    $this->_renderFrame('text', $frame);
                } else {
                    $this->_renderFrame('inline', $frame);
                }
                break;

            case 'table-cell':
                $this->_renderFrame('table-cell', $frame);
                break;

            case 'table-row-group':
            case 'table-header-group':
            case 'table-footer-group':
                $this->_renderFrame('table-row-group', $frame);
                break;

            case '-dompdf-list-bullet':
                $this->_renderFrame('list-bullet', $frame);
                break;

            case '-dompdf-image':
                $this->_renderFrame('image', $frame);
                break;

            case 'none':
                $node = $frame->getNode();

                if ($node->nodeName === 'script') {
                    if ($node->getAttribute('type') === 'text/php' ||
                        $node->getAttribute('language') === 'php'
                    ) {
                        // Evaluate embedded php scripts
                        $this->_renderFrame('php', $frame);
                    } elseif ($node->getAttribute('type') === 'text/javascript' ||
                        $node->getAttribute('language') === 'javascript'
                    ) {
                        // Insert JavaScript
                        $this->_renderFrame('javascript', $frame);
                    }
                }

                // Don't render children, so skip to next iter
                return;

            default:
                break;

        }

        // Starts the overflow: hidden box
        if ($style->overflow === 'hidden') {
            [$x, $y, $w, $h] = $frame->getPaddingBox();

            // get border radii
            $style = $frame->getStyle();
            [$tl, $tr, $br, $bl] = $style->getComputedBorderRadius($w, $h);

            if ($tl + $tr + $br + $bl > 0) {
                $this->_canvas->clippingRoundrectangle($x, $y, (float)$w, (float)$h, $tl, $tr, $br, $bl);
            } else {
                $this->_canvas->clippingRectangle($x, $y, (float)$w, (float)$h);
            }
        }

        $stack = [];

        foreach ($frame->getChildren() as $child) {
            // < 0 : negative z-index
            // = 0 : no z-index, no stacking context
            // = 1 : stacking context without z-index
            // > 1 : z-index
            $child_style = $child->getStyle();
            $child_z_index = $child_style->zIndex;
            $z_index = 0;

           
