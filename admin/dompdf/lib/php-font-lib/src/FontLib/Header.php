<?php

namespace FontLib;

use FontLib\TrueType\File;

/**
 * Font header container.
 *
 * @package php-font-lib
 */
abstract class Header extends BinaryStream
{
    /**
     * @var File
     */
    protected File $font;

    protected array $def;

    public mixed $data = null;

    public function __construct(File $font)
    {
        $this->font = $font;
    }

    abstract public function encode(): string;

    public function parse(): void
    {
        $this->data = $this->font->unpack($this->def);

        if (empty($this->data)) {
            throw new \RuntimeException('Failed to parse header data.');
        }
    }

    public function encode(): string
    {
        if ($this->data === null) {
            throw new \RuntimeException('Header data not set.');
        }

        return $this->font->pack($this->def, $this->data);
    }
}
