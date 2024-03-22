<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;
use Exception;

/**
 * Class head
 *
 * Represents the head font table.
 *
 * @package FontLib\Table\Type
 */
class head extends Table
{
    protected const MAGIC_NUMBER = 0x5F0F3CF5;

    protected $def = [
        // ...
    ];

    /**
     * head constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $data += $this->def;
        parent::_parse($data);
    }

    /**
     * Parses the head table data.
     *
     * @throws Exception
     */
    protected function _parse(): void
    {
        parent::_parse();

        if ($this->data["magicNumber"] !== self::MAGIC_NUMBER) {
            throw new Exception("Incorrect magic number (0x" . dechex($this->data["magicNumber"]) . "). Expected: 0x" . dechex(self::MAGIC_NUMBER) . ".");
        }
    }
}
