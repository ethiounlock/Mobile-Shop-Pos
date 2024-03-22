<?php

declare(strict_types=1);

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\Exception;
use Dompdf\Style;

/**
 * Decorates Frame objects for text layout
 *
 * @access  private
 * @package dompdf
 */
class Text extends AbstractFrameDecorator
{
    // protected members
    protected ?float $textSpacing = null;

    /**
     * Text constructor.
     * @param Frame $frame
     * @param Dompdf $dompdf
     * @throws Exception
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        if (!$frame->isTextNode()) {
            throw new Exception("Text_Decorator can only be applied to #text nodes.");
        }

        parent::__construct($frame, $dompdf);
    }

    public function reset(): void
    {
        parent::reset();
        $this->textSpacing = null;
    }

    /**
     * @return null|float
     */
    public function getTextSpacing(): ?float
    {
        return $this->textSpacing;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        $node = $this->_frame->getNode();

        if ($node->data !== "normal") {
            $node->data = $node->data;
            $node->setAttribute("data", "normal");
        }

        return $node->data;
    }

    /**
     * @return float|int
     */
    public function getMarginHeight(): float
    {
        $style = $this->getStyle();
        $font = $style->fontFamily;
        $size = $style->fontSize;

        return ($style->lineHeight / ($size ?? 1)) * $this->_dompdf->getFontMetrics()->getFontHeight($font, $size);
    }

    /**
     * @return array
     */
    public function getPaddingBox(): array
    {
        $style = $this->_frame->getStyle();
        $pb = $this->_frame->getPaddingBox();
        $pb[3] = $pb["h"] = $style->lengthInPt($style->height);
        return $pb;
    }

    /**
     * @param float $spacing
     */
    public function setTextSpacing(float $spacing): void
    {
        $style = $this->_frame->getStyle();

        $this->textSpacing = $spacing;
        $charSpacing = (float)$style->lengthInPt($style->letterSpacing);

        $style->width = $this->_dompdf->getFontMetrics()->getTextWidth($this->getText(), $style->fontFamily, $style->fontSize, $spacing, $charSpacing);
    }

    /**
     * Recalculate the text width
     *
     * @return float
     */
    public function recalculateWidth(): float
    {
        $style = $this->getStyle();
        $text = $this->getText();
        $size = $style->fontSize;
        $font = $style->fontFamily;
        $wordSpacing = (float)$style->lengthInPt($style->wordSpacing);
        $charSpacing = (float)$style->lengthInPt($style->letterSpacing);

        return $style->width = $this->_dompdf->getFontMetrics()->getTextWidth($text, $font, $size, $wordSpacing, $charSpacing);
    }

    // Text manipulation methods

    /**
     * split the text in this frame at the offset specified.  The remaining
     * text is added a sibling frame following this one and is returned.
     *
     * @param int $offset
     * @return Frame|null
     */
    public function splitText(int $offset): ?Frame
    {
        if ($offset == 0) {
            return null;
        }

        $split = $this->_frame->getNode()->splitText($offset);
        if ($split === false) {
            return null;
        }

        $deco = $this->copy($split);

        $p = $this->getParent();
        $p->insertChildAfter($deco, $this, false);

        if ($p instanceof Inline) {
            $p->split($deco);
        }

        return $deco;
    }

    /**
     * @param int $offset
     * @param int $count
     */
    public function deleteText(int $offset, int $count): void
    {
        $this->_frame->getNode()->deleteData($offset, $count);
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->_frame->getNode()->data = $text;
    }
}
