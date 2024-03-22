<?php

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Positions table rows.
 *
 * @package dompdf
 */
class TableRow extends AbstractPositioner
{

    /**
     * Position a table row frame.
     *
     * @param AbstractFrameDecorator $frame The frame to position.
     *
     * @return void
     */
    public function position(AbstractFrameDecorator $frame): void
    {
        $cb = $frame->get_containing_block();
        $p = $frame->get_prev_sibling();

        if ($p) {
            $y = $p->get_position("y") + $p->get_margin_height();
        } else {
            $y = $cb["y"];
        }

        $frame->set_position($cb["x"], $y);
    }
}
