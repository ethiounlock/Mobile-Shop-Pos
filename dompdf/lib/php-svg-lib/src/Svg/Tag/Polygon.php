<?php
namespace Svg\Tag;

use Svg\Document;
use Svg\Surface;

class Polygon extends Shape
{
    public function start($attributes)
    {
        $points = $this->parsePointsAttribute($attributes['points']);

        $surface = $this->document->getSurface();
        $surface->moveTo($points[0], $points[1]);

        for ($i = 2; $i < count($points); $i += 2) {
            $surface->lineTo($points[$i], $points[$i + 1]);
        }

        $surface->closePath();
    }

    private function parsePointsAttribute(string $pointsAttribute): array
    {
        $points = explode(' ', $pointsAttribute);
        $parsedPoints = [];

        foreach ($points as $point) {
            $coordinates = explode(',', $point);
            $parsedPoints[] = (float) $coordinates[0];
            $parsedPoints[] = (float) $coordinates[1];
        }

        return $parsedPoints;
    }
}
