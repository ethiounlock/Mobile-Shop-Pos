<?php

namespace Dompdf\FrameReflower;

use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\FrameDecorator\Table as TableFrameDecorator;

/**
 * Reflows table row groups (e.g. tbody tags)
 *
 * @package dompdf
 */
class TableRowGroup extends AbstractFrameReflower
{

    /**
     * @var \Dompdf\Frame
     */
    private $frame;

    /**
     * TableRowGroup constructor.
     * @param \Dompdf\Frame $frame
     */
    public function __construct(\Dompdf\Frame $frame)
    {
        parent::__construct($frame);
        $this->frame = $frame;
    }

    /**
     * @param BlockFrameDecorator|null $block
     */
    public function reflow(BlockFrameDecorator $block = null)
    {
        $page = $this->frame->get_root();

        $style = $this->frame->get_style();

        // Find the parent table
        $table = TableFrameDecorator::find_parent_table($this->frame);
        if ($table === null) {
            throw new \Exception("Table row group does not have a parent table.");
        }

        $cb = $this->frame->get_containing_block();

        foreach ($this->frame->get_children() as $child) {
            // Bail if the page is full
            if ($page->is_full()) {
                return;
            }

            $child->set_containing_block($cb["x"], $cb["y"], $cb["w"], $cb["h"]);
            $child->reflow();

            // Check if a split has occured
            $page->check_page_break($child);
        }

        if ($page->is_full()) {
            return;
        }

        $cellmap = $table->get_cellmap();

        // Check if $cellmap is not null before accessing its properties
        if ($cellmap !== null) {
            $style->width = $cellmap->get_frame_width($this->frame);
            $style->height = $cellmap->get_frame_height($this->frame);

            $this->frame->set_position($cellmap->get_frame_position($this->frame));

            if ($table->get_style()->border_collapse === "collapse") {
                // Unset our borders because our cells are now using them
                $style->border_style = "none";
            }
        }
    }
}
