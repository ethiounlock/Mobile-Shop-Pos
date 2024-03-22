<?php

namespace Dompdf\Positioner;

use Dompdf\FrameDecorator\AbstractFrameDecorator;

/**
 * Dummy positioner
 *
 * This class is an abstract class and cannot be instantiated on its own.
 * It is intended to be extended by other classes.
 *
 * @package dompdf
 */
abstract class NullPositioner extends AbstractPositioner
{

    /**
     * Position the frame at (0, 0)
     *
     * This method is final and cannot be overridden in extending classes.
     *
     * @param AbstractFrameDecorator $frame The frame to position
     *
     * @return void
     */
    final public function position(AbstractFrameDecorator $frame): void
    {
        return;
    }
}
