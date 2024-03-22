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
     * @var Table
     */
    protected $table;

    /**
     * @var \DOMMap
     */
    protected $cellmap;

    /**
     * @var \DOMFramePosition
     */
    protected $framePosition;

    /**
     * TableCell constructor.
     *
     * @param Table $table
     * @param \DOMMap $cellmap
     * @param \DOMFramePosition $framePosition
     */
    public function __construct(Table $table, \DOMMap $cellmap, \DOMFramePosition $framePosition)
    {
        $this->table = $table;
        $this->cellmap = $cellmap;
        $this->framePosition = $framePosition;
    }

    /**
     * @param AbstractFrameDecorator $frame
     */
    public function position(AbstractFrameDecorator $frame)
    {
        $frame->set_position($this->cellmap->get_frame_position($frame));
    }
}
