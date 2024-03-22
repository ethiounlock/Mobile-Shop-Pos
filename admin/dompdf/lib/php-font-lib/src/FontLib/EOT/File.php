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
    const TTMBED_EMBEDEUDC = 0x00000020;
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

        if ($string === false) {
            throw new \RuntimeException("Error reading from file.");
        }

        $chunks = str_split($string, 2);
        $chunks = array_map("strrev", $chunks);

        return implode("", $chunks);
    }

    /**
     * Read a 32-bit unsigned integer from the file in little-endian byte order.
     *
     * @return int
     */
    public function readUInt32(): int
    {
        $uint32 = parent::readUInt32();

        return ($uint32 >> 16 & 0x0000FFFF) | ($uint32 << 16 & 0xFFFF0000);
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
    {
        return $this->header->data["StyleName"];
    }

    /**
     * Get font full name.
     *
     * @return string|null
     */
    public function getFontFullName(): ?string
    {
        return $this->header->data["FullName"];
    }

    /**
     * Get font version.
     *
     * @return string|null
     */
    public function getFontVersion(): ?string
    {
        return $this->header->data["VersionName"];
    }

    /**
     * Get font weight.
     *
     * @return string|null
     */
    public function getFontWeight(): ?string
    {
        return isset($this->header->data["Weight"]) ? $this->header->data["Weight"] : null;
    }

    /**
     * Get font Postscript name.
     *
     * @return string|null
     */
    public function getFontPostscriptName(): ?string
    {
        return null;
    }
}
