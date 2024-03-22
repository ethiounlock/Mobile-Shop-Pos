<?php

namespace Svg\Tag;

use Svg\Surface\SurfaceInterface;

class Path extends Shape
{
    /**
     * @var array
     */
    private static $commandLengths = [
        'm' => 2,
        'l' => 2,
        'h' => 1,
        'v' => 1,
        'c' => 6,
        's' => 4,
        'q' => 4,
        't' => 2,
        'a' => 7,
    ];

    /**
     * @var array
     */
    private static $repeatedCommands = [
        'm' => 'l',
        'M' => 'L',
    ];

    /**
     * @param array $attributes
     */
    public function start(array $attributes): void
    {
        if (!isset($attributes['d'])) {
            $this->hasShape = false;

            return;
        }

        $commands = $this->parsePathData($attributes['d']);
        $pathData = $this->parseCommands($commands);

        $surface = $this->document->getSurface();
        $this->drawPath($surface, $pathData);
    }

    /**
     * @param string $pathData
     * @return array
     */
    private function parsePathData(string $pathData): array
    {
        $pattern = '/([MZLHVCSQTAmzlhvcsqta])([eE ,.-.\d]+)*/';
        preg_match_all($pattern, $pathData, $commands, PREG_SET_ORDER);

        return $commands;
    }

    /**
     * @param array $commands
     * @return array
     */
    private function parseCommands(array $commands): array
    {
        $pathData = [];

        foreach ($commands as $command) {
            if (count($command) === 3) {
                $arguments = $this->parseArguments($command[2]);
                $pathData[] = array_merge([$command[1]], $arguments);
            } else {
                $pathData[] = [$command[1]];
            }
        }

        return $pathData;
    }

    /**
     * @param string $arguments
     * @return array
     */
    private function parseArguments(string $arguments): array
    {
        $pattern = '/([-+]?((\d+\.\d+)|((\d+)|(\.\d+)))(?:e[-+]?\d+)?)/i';
        preg_match_all($pattern, $arguments, $items, PREG_PATTERN_ORDER);

        return $items[0];
    }

    /**
     * @param SurfaceInterface $surface
     * @param array $pathData
     */
    private function drawPath(SurfaceInterface $surface, array $pathData): void
    {
        $l = -($this->document->getSurface()->getWidth() / 2 + $this->pathOffsetX);
        $t = -($this->document->getSurface()->getHeight() / 2 + $this->pathOffsetY);

        $current = null;
        $previous = null;
        $x = $y = 0;
        $controlX = $controlY = null;

        foreach ($pathData as $currentCommand) {
            $methodName = 'draw' . $currentCommand[0];
            if (method_exists($this, $methodName)) {
                $this->$methodName($surface, $currentCommand, $x, $y, $l, $t, $controlX, $controlY);
                $previous = $currentCommand;
                $x = $currentCommand[1] ?? $x;
                $y = $currentCommand[2] ?? $y;

                if ($currentCommand[0] === 'c' || $currentCommand[0] === 'C') {
                    $controlX = $currentCommand[3] + $x;
                    $controlY = $currentCommand[4] + $y;
                }
            }
        }
    }

    // Add the rest of the methods like drawM, drawL, drawC, etc.
}
