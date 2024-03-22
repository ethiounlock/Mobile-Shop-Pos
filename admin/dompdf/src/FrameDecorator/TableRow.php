<?php

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\FrameDecorator\Table as TableFrameDecorator;

/**
 * Decorates Frames for table row layout
 *
 * @package dompdf
 */
class TableRow extends AbstractFrameDecorator
{
    /**
     * TableRow constructor.
     * @param Frame $frame
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);
    }

    /**
     * Remove all non table-cell frames from this row and move them after
     * the table.
     */
    public function normalise()
    {
        // Find our table parent
        $parentTable = TableFrameDecorator::find_parent_table($this);

        $erroneousFrames = [];
        foreach ($this->get_children() as $childFrame) {
            $display = $childFrame->get_style()->display;

            if ($display !== "table-cell") {
                $erroneousFrames[] = $childFrame;
            }
        }

        //  Move the extra nodes after the table.
        foreach ($erroneousFrames as $erroneousFrame) {
            $parentTable->move_after($erroneousFrame);
        }
    }

    public function split(Frame $child = null, $forcePageBreak = false)
    {
        $this->_already_pushed = true;

        if (is_null($child)) {
            parent::split();
            return;
        }

        parent::split($child, $forcePageBreak);
    }
}
