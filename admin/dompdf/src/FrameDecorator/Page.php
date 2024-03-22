<?php

namespace Dompdf\FrameDecorator;

use Dompdf\Css\Style;
use Dompdf\Dompdf;
use Dompdf\Helpers;
use Dompdf\Frame;
use Dompdf\Renderer;

/**
 * Decorates frames for page layout
 */
class Page extends AbstractFrameDecorator
{
    /**
     * y value of bottom page margin
     *
     * @var float|null
     */
    protected $bottomPageMargin;

    /**
     * Flag indicating page is full.
     *
     * @var bool
     */
    protected $pageFull;

    /**
     * Number of tables currently being reflowed
     *
     * @var int
     */
    protected $inTable;

    /**
     * The PDF renderer
     *
     * @var Renderer
     */
    protected $renderer;

    /**
     * This page's floating frames
     *
     * @var Frame[]
     */
    protected $floatingFrames = [];

    //........................................................................

    /**
     * Class constructor
     *
     * @param Frame $frame the frame to decorate
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);
        $this->pageFull = false;
        $this->inTable = 0;
        $this->bottomPageMargin = null;
    }

    /**
     * Set the renderer used for this PDF
     *
     * @param Renderer $renderer the renderer to use
     */
    public function setRenderer(Renderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * Return the renderer used for this PDF
     *
     * @return Renderer
     */
    public function getRenderer(): Renderer
    {
        return $this->renderer;
    }

    /**
     * Set the frame's containing block.  Overridden to set $this->bottomPageMargin.
     *
     * @param float|null $x
     * @param float|null $y
     * @param float|null $w
     * @param float|null $h
     */
    public function setContainingBlock(?float $x = null, ?float $y = null, ?float $w = null, ?float $h = null): void
    {
        parent::setContainingBlock($x, $y, $w, $h);

        if (isset($h)) {
            $this->bottomPageMargin = $h - $this->_frame->getStyle()->lengthInPt($this->_frame->getStyle()->marginBottom, $w);
        }
    }

    /**
     * Returns true if the page is full and is no longer accepting frames.
     *
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->pageFull;
    }

    /**
     * Start a new page by resetting the full flag.
     */
    public function nextPage(): void
    {
        $this->floatingFrames = [];
        $this->renderer->newPage();
        $this->pageFull = false;
    }

    /**
     * Indicate to the page that a table is currently being reflowed.
     */
    public function tableReflowStart(): void
    {
        $this->inTable++;
    }

    /**
     * Indicate to the page that table reflow is finished.
     */
    public function tableReflowEnd(): void
    {
        $this->inTable--;
    }

    /**
     * Return whether we are currently in a nested table or not
     *
     * @return bool
     */
    public function inNestedTable(): bool
    {
        return $this->inTable > 1;
    }

    /**
     * Check if a forced page break is required before $frame.  This uses the
     * frame's page_break_before property as well as the preceeding frame's
     * page_break_after property.
     *
     * @link http://www.w3.org/TR/CSS21/page.html#forced
     *
     * @param Frame $frame the frame to check
     *
     * @return bool true if a page break occurred
     */
    public function checkForcedPageBreak(Frame $frame): ?bool
    {
        // Skip check if page is already split
        if ($this->pageFull) {
            return null;
        }

        $blockTypes = ["block", "list-item", "table", "inline"];
        $pageBreaks = ["always", "left", "right"];

        $style = $frame->getStyle();

        if (!in_array($style->display, $blockTypes)) {
            return false;
        }

        // Find the previous block-level sibling
        $prev = $frame->getPrevSibling();

        while ($prev && !in_array($prev->getStyle()->display, $blockTypes)) {
            $prev = $prev->getPrevSibling();
        }

        if (in_array($style->pageBreakBefore, $pageBreaks)) {
            // Prevent cascading splits
            $frame->split(null, true);
            // We have to grab the style again here because split() resets
            // $frame->style to the frame's original style.
            $frame->getStyle()->pageBreakBefore = "auto";
            $this->pageFull = true;
            $frame->_alreadyPushed = true;

            return true;
        }

        if ($prev && in_array($prev->getStyle()->pageBreakAfter, $pageBreaks)) {
            // Prevent casc
