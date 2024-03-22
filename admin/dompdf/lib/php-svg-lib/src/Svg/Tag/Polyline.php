<?php
/**
 * @package php-svg-lib
 * @link    http://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ http://www.gnu.org/copyleft/lesser.html
 */

namespace Svg\Tag;

class Polyline extends Shape
{
    /**
     * @param array $attributes
     */
    public function start(array $attributes): void
    {
        $points = $attributes['points'] ?? '';
        if (empty($points)) {
            return;
        }

        $tmp = [];
        $success = preg_match_all('/([\-]*[0-9\.]+)/', $points, $tmp);
        if (!$success) {
            throw new \RuntimeException('Invalid points attribute');
        }

        $points = $tmp[0];
        $count = count($points);

        $surface = $this->document->getSurface();
        list($x, $y) = $points;
        $surface->moveTo($x, $y);

        for ($i = 2; $i < $count; $i += 2) {
            $x = $points[$i];
            $y = $points[$i + 1];
            $surface->lineTo($x, $y);
        }
    }
}
