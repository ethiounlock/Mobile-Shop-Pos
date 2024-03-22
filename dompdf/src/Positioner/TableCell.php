<?php

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;
use Dompdf\FrameDecorator\Table;

/**
 * Positions table cells
 *
 * @package dompdf
 */
class TableCell extends AbstractPositioner
{

    /**
     * Position a frame
     *
     * @param AbstractFrameDecorator $frame
     */
    public function position(AbstractFrameDecorator $frame): void
    {
        $table = $frame->parent_table;
        $cellmap = $table->cellmap;
        $frame->set_position($cellmap->get_frame_position($frame));
    }
}
