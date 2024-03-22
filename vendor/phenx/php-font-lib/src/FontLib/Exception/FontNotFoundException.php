<?php

namespace FontLib\Exception;

class FontNotFoundException extends \Exception
{
    private $fontPath;

    public function __construct(string $fontPath)
    {
        $this->fontPath = $fontPath;
        parent::__construct(sprintf('Font not found in: %s', $fontPath));
    }

    public function getFontPath(): string
    {
        return $this->fontPath;
    }
}
