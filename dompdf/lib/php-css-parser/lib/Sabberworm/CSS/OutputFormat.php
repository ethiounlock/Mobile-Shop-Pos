<?php

namespace Sabberworm\CSS;

use Sabberworm\CSS\Parsing\OutputException;

class OutputFormat
{
    // ... (previous properties)

    public function __construct()
    {
    }

    public function get(string $name): ?string
    {
        // ... (previous implementation)
    }

    public function set(array|string $names, $value): self
    {
        // ... (previous implementation)
    }

    public function getSpace(string $name, string $type = null): string
    {
        // ... (previous space method implementation)
    }

    public function getSpaceAfterRuleName(): string
    {
        // ... (previous space method implementation)
    }

    // ... (other getSpace* methods)

    public function validate(): void
    {
        // Add your validation logic here
    }

    private function __clone()
    {
        // Clone the object and its nested properties
        $this->oFormatter = clone $this->oFormatter;
        if ($this->oNextLevelFormat !== null) {
            $this->oNextLevelFormat = clone $this->oNextLevelFormat;
        }
    }
}

class OutputFormatter
{
    // ... (previous properties and methods)

    public function safely(callable $cCode): ?string
    {
        // ... (previous safely method implementation)
    }

    private function __clone()
    {
        // Clone the object and its nested properties
        $this->oFormat = clone $this->oFormat;
    }
}

final class FormatFactory
{
    public static function create(): OutputFormat
    {
        return new OutputFormat();
    }

    public static function createCompact(): OutputFormat
    {
        // ... (previous implementation)
    }

    public static function createPretty(): OutputFormat
    {
        // ... (previous implementation)
    }
}
