<?php

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Base AbstractPositioner class
 *
 * Defines postioner interface
 */
abstract class AbstractPositioner
{
    /**
     * Position a frame decorator
     *
     * @param AbstractFrameDecorator $frame
     * @return mixed
     */
    abstract public function position(AbstractFrameDecorator $frame);

    /**
     * Move a frame decorator and its children
     *
     * @param AbstractFrameDecorator $frame
     * @param int $offset_x
     * @param int $offset_y
     * @param bool $ignore_self
     */
    public function move(AbstractFrameDecorator $frame, int $offset_x, int $offset_y, bool $ignore_self = false)
    {
        list($x, $y) = $frame->get_position();

        if (!$ignore_self) {
            $frame->set_position($x + $offset_x, $y + $offset_y);
        }

        foreach ($frame->get_children() as $child) {
            $this->move($child, $offset_x, $offset_y);
        }
    }
}
