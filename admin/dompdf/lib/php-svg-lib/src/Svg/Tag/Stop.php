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
        // Add a check for required attributes
        if (!isset($attributes['offset']) || !isset($attributes['style'])) {
            throw new \Exception('Required attributes "offset" and "style" are missing.');
        }

        // Add validation for the offset attribute
        if (filter_var($attributes['offset'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(0|[1-9]\d*)?(\.\d+)?$/']]) === false) {
            throw new \Exception('The "offset" attribute is not valid.');
        }

        // Add setting of property values
        $this->setOffset($attributes['offset']);
        $this->setStyle($attributes['style']);
    }

    private function setOffset($offset)
    {
        $this->offset = $offset;
    }

    private function setStyle($style)
    {
        $this->style = $style;
    }
}
