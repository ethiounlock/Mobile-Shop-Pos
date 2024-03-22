<?php

declare(strict_types=1);

namespace Dompdf\Renderer;

use Dompdf\Frame;
use Dompdf\FrameDecorator\AbstractFrameDecorator;
use Dompdf\Helpers;

/**
 * Renders block frames
 *
 * @package dompdf
 */
class Block extends AbstractRenderer
{

    /**
     * @param Frame $frame
     */
    public function render(Frame $frame): void
    {
        $style = $frame->getStyle();
        $node = $frame->getNode();
        $dompdf = $this->getDompdf();
        $options = $dompdf->getOptions();

        [$x, $y, $w, $h] = $frame->getBorderBox();

        $this->setOpacity($frame->getOpacity($style->opacity));

        if ($node->nodeName === "body") {
            $h = $frame->getContainingBlock("h") - ($style->marginTop + $style->borderTopWidth + $style->borderBottomWidth + $style->marginBottom);
        }

        // Handle anchors & links
        if ($node->nodeName === "a" && $href = $node->getAttribute("href")) {
            $href = Helpers::buildUrl($dompdf->getProtocol(), $dompdf->getBaseHost(), $dompdf->getBasePath(), $href);
            $this->_canvas->addLink($href, $x, $y, (float)$w, (float)$h);
        }

        // Draw our background, border and content
        [$tl, $tr, $br, $bl] = $style->getComputedBorderRadius($w, $h);

        if ($tl + $tr + $br + $bl > 0) {
            $this->_canvas->clippingRoundrectangle($x, $y, (float)$w, (float)$h, $tl, $tr, $br, $bl);
        }

        if (($bg = $style->backgroundColor) !== "transparent") {
            $this->_canvas->filledRectangle($x, $y, (float)$w, (float)$h, $bg);
        }

        if (($url = $style->backgroundImage) && $url !== "none") {
            $this->_backgroundImage($url, $x, $y, $w, $h, $style);
        }

        if ($tl + $tr + $br + $bl > 0) {
            $this->_canvas->clippingEnd();
        }

        $border_box = [$x, $y, $w, $h];
        $this->_renderBorder($frame, $border_box);
        $this->_renderOutline($frame, $border_box);

        if ($options->getDebugLayout()) {
            if ($options->getDebugLayoutBlocks()) {
                $debug_border_box = $frame->getBorderBox();
                $this->_debugLayout([$debug_border_box['x'], $debug_border_box['y'], (float)$debug_border_box['w'], (float)$debug_border_box['h']], "red");
                if ($options->getDebugLayoutPaddingBox()) {
                    $debug_padding_box = $frame->getPaddingBox();
                    $this->_debugLayout([$debug_padding_box['x'], $debug_padding_box['y'], (float)$debug_padding_box['w'], (float)$debug_padding_box['h']], "red", [0.5, 0.5]);
                }
            }

            if ($options->getDebugLayoutLines() && $frame->getDecorator()) {
                foreach ($frame->getDecorator()->getLineBoxes() as $line) {
                    $frame->_debugLayout([$line->x, $line->y, $line->w, $line->h], "orange");
                }
            }
        }

        $id = $frame->getNode()->getAttribute("id");
        if (strlen($id) > 0) {
            $this->_canvas->addNamedDest($id);
        }
    }

    /**
     * @param AbstractFrameDecorator $frame
     * @param array<int, float> $border_box
     * @param string $corner_style
     */
    protected function _renderBorder(AbstractFrameDecorator $frame, array $border_box = [], string $corner_style = "bevel"): void
    {
        $style = $frame->getStyle();
        $bp = $style->getBorderProperties();

        if (empty($border_box)) {
            $border_box = $frame->getBorderBox();
        }

        // find the radius
        $radius = $style->getComputedBorderRadius($border_box[2], $border_box[3]); // w, h

        // Short-cut: If all the borders are "solid" with the same color and style, and no radius, we'd better draw a rectangle
        if (
            in_array($bp["top"]["style"], ["solid", "dashed", "dotted"]) &&
            $bp["top"] == $bp["right"] &&
            $bp["right"] == $bp["bottom"] &&
            $bp["bottom"] == $bp["left"] &&
            array_sum($radius) == 0
        ) {
            $props = $bp["top"];
            if ($props["color"] === "transparent" || $props["width"] <= 0) {
                return;
            }

            [$x, $y, $w, $h] = $border_box;
            $width = (float)$style->lengthInPt($props["width"]);
            $pattern = $this->_getDashPattern($props["style"], $width);
            $this->_canvas->rectangle($x + $width / 2, $y
