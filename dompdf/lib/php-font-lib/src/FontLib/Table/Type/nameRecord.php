<?php

namespace FontLib\Table\Type;

use FontLib\Font;
use FontLib\BinaryStream;

/**
 * Font table name record.
 *
 * @package php-font-lib
 */
class NameRecord extends BinaryStream
{
    /** @var int */
    public int $platformID;

    /** @var int */
    public int $platformSpecificID;

    /** @var int */
    public int $languageID;

    /** @var int */
    public int $nameID;

    /** @var int */
    public int $length;

    /** @var int */
    public int $offset;

    /** @var string */
    public string $string;

    /**
     * NameRecord constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->map($data);
    }

    /**
     * @param array $data
     */
    public function map(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return string
     */
    public function getUTF8(): string
    {
        return $this->string;
    }

    /**
     * @return string
     */
    public function getUTF16(): string
    {
        return Font::UTF8ToUTF16($this->string);
    }

    /**
     * @return string
     */
    public function
