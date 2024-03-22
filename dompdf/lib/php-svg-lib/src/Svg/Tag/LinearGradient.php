<?php

namespace Svg\Tag;

use Svg\Gradient;
use Svg\Style;

class LinearGradient extends AbstractTag
{
    protected $x1;
    protected $y1;
    protected $x2;
    protected $y2;

    /** @var Gradient\Stop[] */
    protected array $stops = [];

    public function start(array $attributes): void
    {
        parent::start($attributes);

        $this->x1 = $attributes['x1'] ?? null;
        $this->y1 = $attributes['y1'] ?? null;
        $this->x2 = $attributes['x2'] ?? null;
        $this->y2 = $attributes['y2'] ?? null;
    }

    public function getStops(): array
    {
        if (empty($this->stops)) {
            $this->stops = array_filter(array_map(function ($child) {
                if ($child->tagName !== "stop") {
                    return null;
                }

                $attributes = $child->attributes;

                $style = Style::parseCssStyle($attributes["style"] ?? '');
                $offset = $attributes["offset"] ?? null;

                $stop = new Gradient\Stop();

                if (isset($style["stop-color"])) {
                    $stop->color = Style::parseColor($style["stop-color"]);
                }

                if (isset($style["stop-opacity"])) {
                    $stop->opacity = max(0, min(1.0, $style["stop-opacity"]));
                }

                if ($offset) {
                    $stop->offset = $offset;
                }

                if (isset($attributes["stop-color"])) {
                    $stop->color = Style::parseColor($attributes["stop-color"]);
                }

                if (isset($attributes["stop-opacity"])) {
                    $stop->opacity = max(0, min(1.0, $attributes["stop-opacity"]));
                }

                return $stop;
            }, $this->children));
        }

        // Validate offset values
        $this->stops = array_values(array_filter($this->stops, function (Gradient\Stop $stop) {
            return is_numeric($stop->offset) && $stop->offset >= 0 && $stop->offset <= 100;
        }));

        return $this->stops;
    }
}
