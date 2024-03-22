<?php

namespace Dompdf\FrameReflower;

use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Reflows list bullets
 */
class ListBullet extends AbstractFrameReflower
{

    /**
     * ListBullet constructor.
     * @param AbstractFrameDecorator $frame
     */
    public function __construct(AbstractFrameDecorator $frame)
    {
        parent::__construct($frame);
    }

    /**
     * @param BlockFrameDecorator|null $block
     * @return ListBullet
     */
    public function reflow(?BlockFrameDecorator $block = null): ListBullet
    {
        $style = $this->_frame->get_style();

        $style->width = $this->_frame->get_width();
        $this->_frame->position();

        if ($style->list_style_position === "inside") {
            $p = $this->_frame->find_block_parent();
            $p->add_frame_to_line($this->_frame);
        }

        return $this;
    }
}
