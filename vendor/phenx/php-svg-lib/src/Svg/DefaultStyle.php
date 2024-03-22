<?php

namespace Svg;

/**
 * Default style for SVG elements.
 *
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class DefaultStyle extends Style
{
    /**
     * @var string CSS color value for the element's color.
     */
    private $color = '';

    /**
     * @var float Opacity value for the element's color.
     */
    private $opacity = 1.0;

    /**
     * @var string Display value for the element.
     */
    private $display = 'inline';

    /**
     * @var string CSS color value for the element's fill color.
     */
    private $fill = 'black';

    /**
     * @var float Opacity value for the element's fill color.
     */
    private $fillOpacity = 1.0;

    /**
     * @var string Fill rule for the element's fill.
     */
    private $fillRule = 'nonzero';

    /**
     * @var string CSS color value for the element's stroke color.
     */
    private $stroke = 'none';

    /**
     * @var float Opacity value for the element's stroke color.
     */
    private $strokeOpacity = 1.0;

    /**
     * @var string Stroke line cap style for the element's stroke.
     */
    private $strokeLinecap = 'butt';
