<?php

declare(strict_types=1);

namespace Dompdf\Frame;

use Dompdf\Css\Style;
use Dompdf\Dompdf;
use Dompdf\Exception;
use Dompdf\Frame;
use Dompdf\FrameDecorator\AbstractFrameDecorator;
use Dompdf\FrameDecorator\Page as PageFrameDecorator;
use Dompdf\FrameReflower\Page as PageFrameReflower;
use Dompdf\Positioner\AbstractPositioner;

/**
 * Class Factory
 * @access private
 * @package dompdf
 */
class Factory
{
    /**
     * @var AbstractPositioner[]
     */
    protected static array $_positioners;

    /**
     * Decorate the root Frame
     *
     * @param Frame $root
     * @param Dompdf $dompdf
     * @return PageFrameDecorator
     */
    static function decorate_root(Frame $root, Dompdf $dompdf): PageFrameDecorator
    {
        $frame = new PageFrameDecorator($root, $dompdf);
        $frame->set_reflower(new PageFrameReflower($frame));
        $root->set_decorator($frame);

        return $frame;
    }

    /**
     * Decorate a Frame
     *
     * @param Frame $frame
     * @param Dompdf $dompdf
     * @param Frame|null $root
     * @return AbstractFrameDecorator
     * @throws Exception
     */
    static function decorate_frame(Frame $frame, Dompdf $dompdf, ?Frame $root = null): AbstractFrameDecorator
    {
        if (is_null($dompdf)) {
            throw new Exception("The DOMPDF argument is required");
        }

        $style = $frame->get_style();

        if (is_null($style)) {
            return;
        }

        // Floating (and more generally out-of-flow) elements are blocks
        // http://coding.smashingmagazine.com/2007/05/01/css-float-theory-things-you-should-know/
        if (!$frame->is_in_flow() && in_array($style->display, Style::$INLINE_TYPES)) {
            $style->display = "block";
        }

        $display = $style->display;

        // ... (rest of the method left unchanged)
    }

    /**
     * Creates Positioners
     *
     * @param string $type type of positioner to use
     * @return AbstractPositioner
     */
    protected static function getPositionerInstance(string $type): AbstractPositioner
    {
        // ... (rest of the method left unchanged)
    }
}
