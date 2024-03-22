<?php

namespace Dompdf\Renderer;

use Dompdf\Frame;
use Dompdf\Helpers;

/**
 * Renders inline frames
 *
 * @access  private
 * @package dompdf
 */
class Inline extends AbstractRenderer
{
    /**
     * @param Frame $frame
     */
    public function render(Frame $frame): void
    {
        if (!$frame->get_first_child()) {
            return; // No children, no service
        }

        $style = $frame->get_style();
        $dompdf = $this->_dompdf;

        // Draw the left border if applicable
        $bp = $style->get_border_properties();
        $widths = [
            ($border_width = $bp["top"]["width"] !== '' ? (float)$style->length_in_pt($bp["top"]["width"]) : 0),
            ($border_width = $bp["right"]["width"] !== '' ? (float)$style->length_in_pt($bp["right"]["width"]) : 0),
            ($border_width = $bp["bottom"]["width"] !== '' ? (float)$style->length_in_pt($bp["bottom"]["width"]) : 0),
            ($border_width = $bp["left"]["width"] !== '' ? (float)$style->length_in_pt($bp["left"]["width"]) : 0)
        ];

        // Draw the background & border behind each child.  To do this we need
        // to figure out just how much space each child takes:
        list($x, $y) = $frame->get_first_child()->get_position();

        $this->_set_opacity($frame->get_opacity($style->opacity));

        $do_debug_layout_line = $dompdf->getOptions()->getDebugLayout()
            && $dompdf->getOptions()->getDebugLayoutInline();

        list($w, $h) = $this->get_child_size($frame, $do_debug_layout_line);

        // make sure the border and background start inside the left margin
        $left_margin = (float)$style->length_in_pt($style->margin_left);
        $x += $left_margin;

        // Handle the last child
        if ($bg = $style->background_color) {
            $bg = $bg !== 'transparent' ? $bg : null;
            $this->_canvas->filled_rectangle($x + $widths[3], $y + $widths[0], $w, $h, $bg);
        }

        // On continuation lines (after line break) of inline elements, the style got copied.
        // But a non repeatable background image should not be repeated on the next line.
        // But removing the background image above has never an effect, and removing it below
        // removes it always, even on the initial line.
        // Need to handle it elsewhere, e.g. on certain ...clone()... usages.
        // Repeat not given: default is Style::__construct
        // ... && (!($repeat = $style->background_repeat) || $repeat === "repeat" ...
        // different position? $this->_background_image($url, $x, $y, $w, $h, $style);
        if ($url = $style->background_image && $url !== 'none') {
            $this->_background_image($url, $x + $widths[3], $y + $widths[0], $w, $h, $style);
        }

        // Add the border widths
        $w += $border_width[1] + $border_width[3];
        $h += $border_width[0] + $border_width[2];

        // If this is the first row, draw the left border too
        if ($bp["left"]["style"] !== 'none' && $bp["left"]["color"] !== 'transparent' && $widths[3] > 0) {
            $method = '_border_' . $bp["left"]["style"];
            $this->$method($x, $y, $h, $bp["left"]["color"], $widths, 'left');
        }

        // Draw the top & bottom borders
        if ($bp["top"]["style"] !== 'none' && $bp["top"]["color"] !== 'transparent' && $widths[0] > 0) {
            $method = '_border_' . $bp["top"]["style"];
            $this->$method($x, $y, $w, $bp["top"]["color"], $widths, 'top');
        }

        if ($bp["bottom"]["style"] !== 'none' && $bp["bottom"]["color"] !== 'transparent' && $widths[2] > 0) {
            $method = '_border_' . $bp["bottom"]["style"];
            $this->$method($x, $y + $h, $w, $bp["
