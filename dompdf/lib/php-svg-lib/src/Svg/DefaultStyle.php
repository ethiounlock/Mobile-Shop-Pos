<?php

namespace Svg\Styles;

/**
 * Default style for SVG elements.
 *
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */
class DefaultStyle extends \Svg\Style
{
    /**
     * DefaultStyle constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->color = '';
        $this->opacity = 1.0;
        $this->display = 'inline';

        $this->fill = 'black';
        $this->fillOpacity = 1.0;
        $this->fillRule = 'nonzero';

        $this->stroke = 'none';
        $this->strokeOpacity = 1.0;
        $this->strokeLinecap = 'butt';
        $this->strokeLinejoin = 'miter';
        $this->strokeMiterlimit = 4;
        $this->strokeWidth = 1.0;
        $this->strokeDasharray = 0;
        $this->strokeDashoffset = 0;
    }
}
