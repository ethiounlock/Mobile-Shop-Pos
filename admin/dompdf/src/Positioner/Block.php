<?php

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Positions block frames
 *
 * @access  private
 * @package dompdf
 */
class Block extends AbstractPositioner
{
    function position(AbstractFrameDecorator $frame)
    {
        $style = $frame->get_style();
        $containingBlock = $frame->get_containing_block();
        $parentBlock = $frame->find_block_parent();

        $x = $containingBlock["x"];
        $y = $containingBlock["y"];

        if ($parentBlock) {
            $float = $style->float;

            if (!$float || $float === "none") {
                $parentBlock->add_line(true);
            }

            $y = $parentBlock->get_current_line_box()->y;
        }

        // Relative positionning
        if ($style->position === "relative") {
            $top = (float)$style->length_in_pt($style->top, $containingBlock["h"]);
            $left = (float)$style->length_in_pt($style->left, $containingBlock["w"]);

            $x += $left;
            $y += $top;
        }

        $frame->set_position($x, $y);
    }
}
