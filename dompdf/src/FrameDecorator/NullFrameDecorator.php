<?php

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;

/**
 * Dummy decorator
 *
 * @package dompdf
 */
class NullFrameDecorator extends AbstractFrameDecorator
{
    /**
     * NullFrameDecorator constructor.
     * @param Frame $frame
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);
        $style = $this->_frame->getStyle();
        $style->width = 0;
        $style->height = 0;
        $style->margin = 0;
        $style->padding = 0;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return '';
    }
}
