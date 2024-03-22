<?php

namespace Sabberworm\CSS;

interface Renderable
{
    public function __toString(): string;

    public function render(OutputFormat $oOutputFormat): string;

    public function getLineNumber(): int;
}
