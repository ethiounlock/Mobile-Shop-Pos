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
     * @param string $attributes
     * @return void
     */
    public function start(string $attributes): void
    {
        $tmp = [];
        $result = preg_match_all('/([\-]*[0-9\.]+)/', $attributes['points'] ?? '', $tmp);

        if ($result === false) {
            // Handle error
            throw new \RuntimeException('preg_match_all failed');
        }

        $points = $tmp[0] ?? [];
        $count = count($points);

        if ($count < 2) {
            // Handle error
            throw new \InvalidArgumentException('Not enough points provided');
        }

        $surface = $this->document->getSurface();
        list($x, $y) = $points;
        $surface->moveTo($x, $y);

        for ($i = 2; $i < $count; $i += 2) {
            if (!isset($points[$i], $points[$i + 1])) {
                // Handle error
                throw new \InvalidArgumentException('Invalid points provided');
            }

            $x = $points[$i];
            $y = $points[$i + 1];
            $surface->lineTo($x, $y);
        }
    }
}
