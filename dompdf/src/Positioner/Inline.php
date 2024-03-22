<?php

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;
use Dompdf\FrameDecorator\Inline as InlineFrameDecorator;
use Dompdf\Exception;

/**
 * Positions inline frames
 *
 * @package dompdf
 */
class Inline extends AbstractPositioner
{

    /**
     * @param AbstractFrameDecorator $frame
     * @return void
     * @throws Exception
     */
    public function position(AbstractFrameDecorator $frame): void
    {
        $p = $this->getBlockLevelParent($frame);

        if (!$p) {
            throw new Exception("No block-level parent found.  Not good.");
        }

        $this->positionFrame($frame, $p);
    }

    /**
     * Position the given frame inside the block-level parent
     *
     * @param AbstractFrameDecorator $frame
     * @param AbstractFrameDecorator $blockParent
     * @return void
     */
    protected function positionFrame(AbstractFrameDecorator $frame, AbstractFrameDecorator $blockParent): void
    {
        $cb = $frame->get_containing_block();
        $line = $blockParent->get_current_line_box();

        $isFixedPositionElement = false;
        $f = $frame;
        while ($f = $f->get_parent()) {
            if ($f->get_style()->position === "fixed") {
                $isFixedPositionElement = true;
                break;
            }
        }

        if (!$isFixedPositionElement && $f->get_parent() &&
            $f->get_parent() instanceof InlineFrameDecorator &&
            $f->is_text_node()
        ) {
            $minMax = $f->get_reflower()->get_min_max_width();

            if ($minMax["min"] > ($cb["w"] - $line->left - $line->w - $line->right)) {
                $blockParent->add_line();
                $line = $blockParent->get_current_line_box();
            }
        }

        $frame->set_position($cb["x"] + $line->w, $line->y);
    }

    /**
     * Get the nearest block-level parent of the given frame
     *
     * @param AbstractFrameDecorator $frame
     * @return AbstractFrameDecorator|null
     */
    protected function getBlockLevelParent(AbstractFrameDecorator $frame): ?AbstractFrameDecorator
    {
        $f = $frame;
        do {
            $f = $f->find_block_parent();
        } while ($f && !$f->isBlock());

        return $f;
    }
}
