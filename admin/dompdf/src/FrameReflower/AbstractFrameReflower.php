<?php

declare(strict_types=1);

namespace Dompdf\FrameReflower;

use Dompdf\Adapter\CPDF;
use Dompdf\Css\Style;
use Dompdf\Dompdf;
use Dompdf\Helpers;
use Dompdf\Frame;
use Dompdf\FrameDecorator\Block;
use Dompdf\Frame\Factory;

/**
 * Base reflower class
 *
 * Reflower objects are responsible for determining the width and height of
 * individual frames.  They also create line and page breaks as necessary.
 *
 * @package dompdf
 */
abstract class AbstractFrameReflower
{
    /**
     * @var Frame
     */
    protected $_frame;

    /**
     * @var array|null
     */
    protected $_min_max_cache;

    /**
     * AbstractFrameReflower constructor.
     * @param Frame $frame
     */
    public function __construct(Frame $frame)
    {
        $this->_frame = $frame;
        $this->_min_max_cache = null;
    }

    public function dispose(): void
    {
    }

    /**
     * @return Dompdf
     */
    public function get_dompdf(): Dompdf
    {
        return $this->_frame->get_dompdf();
    }

    /**
     * Collapse frames margins
     * http://www.w3.org/TR/CSS2/box.html#collapsing-margins
     */
    protected function _collapse_margins(): void
    {
        $frame = $this->_frame;
        $cb = $frame->get_containing_block();
        $style = $frame->get_style();

        // Margins of float/absolutely positioned/inline-block elements do not collapse.
        if (!$frame->is_in_flow() || $frame->is_inline_block() || $frame->get_root() == $frame || $frame->get_parent() == $frame->get_root()) {
            return;
        }

        $t = $style->length_in_pt($style->margin_top, $cb["h"] ?? 0);
        $b = $style->length_in_pt($style->margin_bottom, $cb["h"] ?? 0);

        // Handle 'auto' values
        if ($t === "auto") {
            $style->margin_top = "0pt";
            $t = 0;
        }

        if ($b === "auto") {
            $style->margin_bottom = "0pt";
            $b = 0;
        }

        // Collapse vertical margins:
        $n = $frame->get_next_sibling();
        if ($n && !$n->is_block() && !$n->is_table()) {
            while ($n = $n->get_next_sibling()) {
                if ($n->is_block() || $n->is_table()) {
                    break;
                }

                if (!$n->get_first_child()) {
                    $n = null;
                    break;
                }
            }
        }

        if ($n) {
            $n_style = $n->get_style();
            $n_t = (float)$n_style->length_in_pt($n_style->margin_top, $cb["h"] ?? 0);

            $b = $this->_get_collapsed_margin_length($b, $n_t);
            $style->margin_bottom = $b . "pt";
            $n_style->margin_top = "0pt";
        }

        // Collapse our first child's margin, if there is no border or padding
        if ($style->border_top_width == 0 && $style->length_in_pt($style->padding_top) == 0) {
            $f = $this->_frame->get_first_child();
            if ($f && !$f->is_block() && !$f->is_table()) {
                while ($f = $f->get_next_sibling()) {
                    if ($f->is_block() || $f->is_table()) {
                        break;
                    }

                    if (!$f->get_first_child()) {
                        $f = null;
                        break;
                    }
                }
            }

            // Margin are collapsed only between block-level boxes
            if ($f) {
                $f_style = $f->get_style();
                $f_t = (float)$f_style->length_in_pt($f_style->margin_top, $cb["h"] ?? 0);

                $t = $this->_get_collapsed_margin_length($t, $f_t);
                $style->margin_top = $t . "pt";
                $f_style->margin_top = "0pt";
            }
        }

        // Collapse our last child's margin, if there is no border or padding
        if ($style->border_bottom_width == 0 && $style->length_in_pt
