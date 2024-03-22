<?php

namespace FontLib\EOT;

use FontLib\TrueType\File as TrueTypeFile;

/**
 * EOT font file.
 */
class File extends TrueTypeFile
{
    const TTEMBED_SUBSET = 0x00000001;
    const TTEMBED_TTCOMPRESSED = 0x00000004;
    const TTEMBED_FAILIFVARIATIONSIMULATED = 0x00000010;
    const TTEMBED_EMBEDEUDC = 0x00000020;
    const TTEMBED_VALIDATIONTESTS = 0x00000040; // Deprecated
    const TTEMBED_WEBOBJECT = 0x00000080;
    const TTEMBED_XORENCRYPTDATA = 0x10000000;

    /**
     * @var Header
     */
    public $header;

    /**
     * Parse the EOT file header.
     */
    public function parseHeader(): void
    {
        if (!empty($this->header)) {
            return;
        }

        $this->header = new Header($this);
        $this->header->parse();
    }

    /**
     * Parse the EOT file.
     */
    public function parse(): void
    {
        $this->parseHeader();

        $flags = $this->header->data["Flags"];

        if ($flags & self::TTEMBED_TTCOMPRESSED) {
            $mtx_version = $this->readUInt8();
            $mtx_copy_limit = $this->readUInt24();
            $mtx_offset_1 = $this->readUInt32();
            $mtx_offset_2 = $this->readUInt32();
        }

        if ($flags & self::TTEMBED_XORENCRYPTDATA) {
            // Process XOR
        }

        // TODO Read font data ...
    }

    /**
     * Read a little-endian value from the file.
     *
     * @param int $n The number of bytes to read
     *
     * @return string
     */
    public function read(int $n): string
    {
        if ($n < 1) {
            return "";
        }

        $string = fread($this->f, $n);

        if (false === $string) {
            throw new \RuntimeException('Error reading file');
        }

        return $string;
    }

    /**
     * Read a little-endian 32-bit unsigned integer from the file.
     *
     * @return int
     */
    public function readUInt32(): int
    {
        $uint32 = $this->readUInt16();
        $uint32 |= $this->readUInt16() << 16;

        return $uint32;
    }

    /**
     * Read a little-endian 16-bit unsigned integer from the file.
     *
     * @return int
     */
    public function readUInt16(): int
    {
        $uint16 = $this->readUInt8();
        $uint16 |= $this->readUInt8() << 8;

        return $uint16;
    }

    /**
     * Read a little-endian 24-bit unsigned integer from the file.
     *
     * @return int
     */
    public function readUInt24(): int
    {
        $uint24 = $this->readUInt8();
        $uint24 |= $this->readUInt8() << 8;
        $uint24 |= $this->readUInt8() << 16;

        return $uint24;
    }

    /**
     * Read a single byte from the file.
     *
     * @return int
     */
    public function readUInt8(): int
    {
        $uint8 = $this->read(1);

        if (!is_numeric($uint8)) {
            throw new \RuntimeException('Error reading file');
        }

        return ord($uint8);
    }

    /**
     * Get font copyright.
     *
     * @return string|null
     */
    public function getFontCopyright(): ?string
    {
        return null;
    }

    /**
     * Get font name.
     *
     * @return string|null
     */
    public function getFontName(): ?string
    {
        return $this->header->data["FamilyName"];
    }

    /**
     * Get font subfamily.
     *
     * @return string|null
     */
    public function getFontSubfamily(): ?string
    {
        return $this->header->data["StyleName"];
    }

    /**
     * Get font subfamily ID.
     *
     * @return string|null
     */
    public function getFontSubfamilyID(): ?string

