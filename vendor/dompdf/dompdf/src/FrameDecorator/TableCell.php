<?php

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;

/**
 * Decorates table cells for layout
 *
 * @package dompdf
 */
class TableCell extends BlockFrameDecorator
{

    /**
     * @var array
     */
    protected $_resolved_borders = [];

    /**
     * @var int
     */
    protected $_content_height;

    //........................................................................

    /**
     * TableCell constructor.
     * @param Frame $frame
     * @param Dompdf $dompdf
     */
    public function __construct(Frame $frame, Dompdf $dompdf)
    {
        parent::__construct($frame, $dompdf);
        $this->_content_height = 0;
    }

    //........................................................................

    public function reset()
    {
        parent::reset();
        $this->_resolved_borders = [];
        $this->_content_height = 0;
        $this->_frame->reset();
    }

    /**
     * @return int
     */
    public function getContentHeight(): int
    {
        return $this->_content_height;
    }

    /**
     * @param int $height
     */
    public function setContentHeight(int $height): void
    {
        $this->_content_height = $height;
    }

    /**
     * @param string $side
     * @param array $border_spec
     */
    public function setResolvedBorder(string $side, array $border_spec): void
    {
        $this->_resolved_borders[$side] = $border_spec;
    }

    /**
     * @param string $side
     * @return array
     */
    public function getResolvedBorder(string $side): array
    {
        return $this->_resolved_borders[$side] ?? [];
    }

    /**
     * @return array
     */
    public function getResolvedBorders(): array
    {
        return $this->_resolved_borders;
    }

    /**
     * @param float $height
     */
    public function setCellHeight(float $height): void
    {
        $style = $this->getStyle();
        $vSpace = (float)$style->lengthInPt(
            [
                $style->margin_top,
                $style->padding_top,
                $style->border_top_width,
                $style->border_bottom_width,
                $style->padding_bottom,
                $style->margin_bottom
            ],
            (float)$style->lengthInPt($style->height)
        );

        $newHeight = $height - $vSpace;
        $style->height = $newHeight;

        if ($newHeight > $this->_content_height) {
            $yOffset = 0;

            // Adjust our vertical alignment
            switch ($style->vertical_align) {
                default:
                case "baseline":
                    // Move the content up so the baseline of the first line is aligned with the cell's baseline
                    $yOffset = $this->getBaselineOffset();
                    break;
                case "top":
                    // Don't need to do anything
                    return;

                case "middle":
                    $yOffset = ($newHeight - $this->_content_height) / 2;
                    break;

                case "bottom":
                    $yOffset = $newHeight - $this->_content_height;
                    break;
            }

            if ($yOffset) {
                // Move our children
                foreach ($this->getLineBoxes() as $line) {
                    foreach ($line->getFrames() as $frame) {
                        $frame->move(0, $yOffset);
                    }
                }
            }
        }
    }

    /**
     * Calculate the offset needed to align the first line's baseline with the cell's baseline
     *
     * @return float
     */
    protected function getBaselineOffset(): float
    {
        // Calculate the height of the first line box
        $lineBoxes = $this->getLineBoxes();
        $firstLineBox = reset($lineBoxes);
        $firstLineHeight = $firstLineBox->getHeight();

        // Calculate the distance from the top of the cell to the top of the first line box
        $style = $this->getStyle();
        $topOffset = $style->lengthInPt(
            [
                $style->margin_top,
                $style->padding_top,
                $style->border_top_width
            ]
        );

        // Calculate the distance from the top of the first line box to its baseline
        $fontMetrics = $this->getDompdf()->getFontMetrics();
        $baselineOffset = $fontMetrics->getBaselineOffset($this->getFont());

        // Calculate the total offset needed to align the first line's baseline with the cell's baseline
        return $firstLineHeight - $baselineOffset - $topOffset;
    }
}
