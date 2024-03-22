<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Tag;

class RadialGradient extends AbstractTag
{
    public function start($attributes)
    {
        // Initialize radial gradient properties
        $this->setAttribute('gradientUnits', 'userSpaceOnUse');
        $this->setAttribute('spreadMethod', 'pad');

        // Parse and set the cx, cy, and r attributes
        if (isset($attributes['cx'])) {
            $this->setAttribute('cx', floatval($attributes['cx']));
        }
        if (isset($attributes['cy'])) {
            $this->setAttribute('cy', floatval($attributes['cy']));
        }
        if (isset($attributes['r'])) {
            $this->setAttribute('r', floatval($attributes['r']));
        }
    }
}
