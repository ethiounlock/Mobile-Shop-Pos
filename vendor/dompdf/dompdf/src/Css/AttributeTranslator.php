<?php

namespace Dompdf\Css;

use Dompdf\Frame;

/**
 * Translates HTML 4.0 attributes into CSS rules
 *
 * @package dompdf
 */
class AttributeTranslator
{
    const STYLE_ATTR = "_html_style_attribute";

    // Munged data originally from
    // http://www.w3.org/TR/REC-html40/index/attributes.html
    // http://www.cs.tut.fi/~jkorpela/html2css.html
    private static $ATTRIBUTE_LOOKUP = [
        //'caption' => array ( 'align' => '', ),
        'img' => [
            'align' => [
                'bottom' => 'vertical-align: baseline;',
                'middle' => 'vertical-align: middle;',
                'top' => 'vertical-align: top;',
                'left' => 'float: left;',
                'right' => 'float: right;'
            ],
            'border' => 'border: %0.2Fpx solid;',
            'height' => 'height: %spx;',
            'hspace' => 'padding-left: %1$0.2Fpx; padding-right: %1$0.2Fpx;',
            'vspace' => 'padding-top: %1$0.2Fpx; padding-bottom: %1$0.2Fpx;',
            'width' => 'width: %spx;',
        ],
        'table' => [
            'align' => [
                'left' => 'margin-left: 0; margin-right: auto;',
                'center' => 'margin-left: auto; margin-right: auto;',
                'right' => 'margin-left: auto; margin-right: 0;'
            ],
            'bgcolor' => 'background-color: %s;',
            'border' => '!set_table_border',
            'cellpadding' => '!set_table_cellpadding', //'border-spacing: %0.2F; border-collapse: separate;',
            'cellspacing' => '!set_table_cellspacing',
            'frame' => [
                'void' => 'border-style: none;',
                'above' => 'border-top-style: solid;',
                'below' => 'border-bottom-style: solid;',
                'hsides' => 'border-left-style: solid; border-right-style: solid;',
                'vsides' => 'border-top-style: solid; border-bottom-style: solid;',
                'lhs' => 'border-left-style: solid;',
                'rhs' => 'border-right-style: solid;',
                'box' => 'border-style: solid;',
                'border' => 'border-style: solid;'
            ],
            'rules' => '!set_table_rules',
            'width' => 'width: %s;',
        ],
        'hr' => [
            'align' => '!set_hr_align', // Need to grab width to set 'left' & 'right' correctly
            'noshade' => 'border-style: solid;',
            'size' => '!set_hr_size', //'border-width: %0.2F px;',
            'width' => 'width: %s;',
        ],
        'div' => [
            'align' => 'text-align: %s;',
        ],
        'h1' => [
            'align' => 'text-align: %s;',
        ],
        'h2' => [
            'align' => 'text-align: %s;',
        ],
        'h3' => [
            'align' => 'text-align: %s;',
        ],
        'h4' => [
            'align' => 'text-align: %s;',
        ],
        'h5' => [
            'align' => 'text-align: %s;',
        ],
        'h6' => [
            'align' => 'text-align: %s;',
        ],
        //TODO: translate more form element attributes
        'input' => [
            'size' => '!set_input_width'
        ],
        'p' => [
            'align' => 'text-align: %s;',
        ],
//    'col' => array(
//      'align'  => '',
//      'valign' => '',
//    ),
//    'colgroup' => array(
//      'align'  => '',
//      'valign' => '',
//    ),
        'tbody' => [
            'align' => '!set_table_row_align',
            'valign' => '!set_table_row_valign',
        ],
        'td' => [
            'align' => 'text-align: %s;',
            'bgcolor' => '!set_background_color',
            'height' => 'height: %s;',
            'nowrap' => 'white-space: nowrap;',
            'valign' => 'vertical-align: %s;',
            'width' => 'width: %s;',
        ],
        'tfoot' => [
            'align' => '!set_table_row_align',
            'valign' => '!set_table_row_valign',
        ],
        'th' => [
            'align' => 'text-align: %s;',
            'bgcolor' => '!set_background_color',
            'height' => 'height: %s;',
            'nowrap' => 'white-space: nowrap;',
            'valign' => 'vertical-align: %s;',

