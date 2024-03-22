<?php

declare(strict_types=1);

namespace Dompdf\FrameReflower;

use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\FrameDecorator\Page as PageFrameDecorator;

/**
 * Reflows pages
 *
 * @package dompdf
 */
class Page extends AbstractFrameReflower
{

    /**
     * Cache of the callbacks array
     *
     * @var callable[]|null
     */
    private ?array $callbacks = null;

    /**
     * Cache of the canvas
     *
     * @var \Dompdf\Canvas
     */
    private \Dompdf\Canvas $canvas;

    /**
     * Page constructor.
     *
     * @param PageFrameDecorator $frame
     */
    public function __construct(PageFrameDecorator $frame)
    {
        parent::__construct($frame);
    }

    /**
     * Applies page style to the frame
     *
     * @param Frame $frame
     * @param int $page_number
     */
    private function applyPageStyle(Frame $frame, int $page_number): void
    {
        $style = $frame->getStyle();
        $pageStyles = $style->getStylesheet()->getPageStyles();

        if (count($pageStyles) > 1) {
            $odd = $page_number % 2 === 1;
            $first = $page_number === 1;

            $style = clone $pageStyles["base"];

            foreach (["right", "odd", "left", "even", "first"] as $key) {
                if (isset($pageStyles[":{$key}"]) && ($key === "odd" xor !$odd) && ($key === "even" xor $odd) && ($key === "first" xor $first)) {
                    $style->merge($pageStyles[":{$key}"]);
                }
            }

            $frame->setStyle($style);
        }
    }

    /**
     * Paged layout:
     * http://www.w3.org/TR/CSS21/page.html
     *
     * @param BlockFrameDecorator|null $block
     */
    public function reflow(?BlockFrameDecorator $block = null): void
    {
        $fixedChildren = [];
        $prevChild = null;
        $child = $this->_frame->getFirstChild();
        $currentPage = 0;

        while ($child) {
            $this->applyPageStyle($this->_frame, $currentPage + 1);

            $cb = $this->_frame->getContainingBlock();
            $left = (float)$style->lengthInPt($style->marginLeft, $cb["w"]);
            $right = (float)$style->lengthInPt($style->marginRight, $cb["w"]);
            $top = (float)$style->lengthInPt($style->marginTop, $cb["h"]);
            $bottom = (float)$style->lengthInPt($style->marginBottom, $cb["h"]);

            $contentX = $cb["x"] + $left;
            $contentY = $cb["y"] + $top;
            $contentWidth = $cb["w"] - $left - $right;
            $contentHeight = $this->_frame->getAvailableHeight() - $top - $bottom;

            if ($currentPage === 0) {
                $children = $child->getChildren();
                foreach ($children as $oneChild) {
                    if ($oneChild->getStyle()->position === "fixed") {
                        $fixedChildren[] = $oneChild->deepCopy();
                    }
                }
                $fixedChildren = array_reverse($fixedChildren);
            }

            $child->setContainingBlock($contentX, $contentY, $contentWidth, $contentHeight);

            $this->_checkCallbacks("begin_page_reflow", $child);

            if ($currentPage >= 1) {
                foreach ($fixedChildren as $fixedChild) {
                    $child->insertChildBefore($fixedChild->deepCopy(), $child->getFirstChild());
                }
            }

            $child->reflow();
            $nextChild = $child->getNextSibling();

            $this->_checkCallbacks("begin_page_render", $child);

            $this->_frame->getRenderer()->render($child);

            $this->_checkCallbacks("end_page_render", $child);

            if ($nextChild) {
                $this->_frame->nextPage();
            }

            if ($prevChild) {
                $prevChild->dispose(true);
            }
            $prevChild = $child;
            $child = $nextChild;
            $currentPage++;
        }

        if ($prevChild) {
            $prevChild->dispose(true);
        }
    }

    /**
     * Check for callbacks that need to be performed when a given event
     * gets triggered on a page
     *
     * @param string $event the type of event
     * @param Frame $frame  the frame that event is triggered on
     */
    protected function _checkCallbacks(string $event, Frame $frame): void
    {
        if (!$this->callbacks) {
            $dompdf = $this->_frame->getDompdf();
            $this->callbacks = $dompdf->getCallbacks();
            $this->canvas = $dompdf->getCanvas();
        }

        if (is_array($this->callbacks) && isset($this->callbacks[$event])) {
            $info = [
                0 => $this->canvas, "canvas" => $this->canvas,
                
