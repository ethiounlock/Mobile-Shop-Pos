<?php

namespace Dompdf\FrameDecorator;

use DOMElement;
use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\Exception;

/**
 * Decorates frames for inline layout
 *
 * @access  private
 * @package dompdf
 */
class Inline extends AbstractFrameDecorator
{

    /**
     * Inline constructor.
     * @param Frame $frame
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);
    }

    /**
     * @param Frame|null $frame
     * @param bool $forcePageBreak
     * @throws Exception
     */
    public function split(Frame $frame = null, bool $forcePageBreak = false): void
    {
        if (is_null($frame)) {
            $this->getParent()->split($this, $forcePageBreak);
            return;
        }

        if ($frame->getParent() !== $this) {
            throw new Exception("Unable to split: frame is not a child of this one.");
        }

        $node = $this->getFrame()->getNode();

        if ($node instanceof DOMElement && $node->hasAttribute("id")) {
            $node->setAttribute("data-dompdf-original-id", $node->getAttribute("id"));
            $node->removeAttribute("id");
        }

        $split = $this->copy($node->cloneNode());

        // if this is a generated node don't propagate the content style
        if ($split->getNode()->nodeName == "dompdf_generated") {
            $split->getStyle()->content = "normal";
        }

        $this->getParent()->insertChildAfter($split, $this);

        // Unset the current node's right style properties
        $style = $this->getFrame()->getStyle();
        $style->marginRight = 0;
        $style->paddingRight = 0;
        $style->borderRightWidth = 0;

        // Unset the split node's left style properties since we don't want them
        // to propagate
        $style = $split->getStyle();
        $style->marginLeft = 0;
        $style->paddingLeft = 0;
        $style->borderLeftWidth = 0;

        //On continuation of inline element on next line,
        //don't repeat non-vertically repeatble background images
        //See e.g. in testcase image_variants, long desriptions
        if (($url = $style->backgroundImage) && $url !== "none"
            && ($repeat = $style->backgroundRepeat) && $repeat !== "repeat" && $repeat !== "repeat-y"
        ) {
            $style->backgroundImage = "none";
        }

        // Add $frame and all following siblings to the new split node
        $iterator = $frame;
        while ($iterator) {
            $frame = $iterator;
            $iterator = $iterator->getNextSibling();
            $frame->reset();
            $split->appendChild($frame);
        }

        $pageBreaks = ["always", "left", "right"];
        $frameStyle = $frame->getStyle();
        if ($forcePageBreak ||
            in_array($frameStyle->pageBreakBefore, $pageBreaks) ||
            in_array($frameStyle->pageBreakAfter, $pageBreaks)
        ) {
            $this->getParent()->split($split, true);
        }
    }

}
