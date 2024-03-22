<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Tag;

class Stop extends AbstractTag
{
    public function start($attributes)
    {
        $offset = isset($attributes['offset']) ? (int)$attributes['offset'] : 0;
        $style = isset($attributes['style']) ? $attributes['style'] : '';

        // Add the stop element to the parent element's stop array
        $this->parent->addStop(compact('offset', 'style'));
    }
}
