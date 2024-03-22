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
    protected $font;

    protected string $data;

    public function __construct(File $font)
    {
        $this->font = $font;
    }

    abstract protected function getDef(): array;

    public function encode(): ?string
    {
        if (!$this->data) {
            return null;
        }

        return $this->font->pack($this->getDef(), $this->data);
    }

    public function parse(): void
    {
        $this->data = $this->font->unpack($this->getDef());

        if (empty($this->data)) {
            throw new \RuntimeException('Failed to parse header data');
        }
    }
}
