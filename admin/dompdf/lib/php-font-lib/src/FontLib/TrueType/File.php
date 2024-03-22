<?php

declare(strict_types=1);

namespace FontLib\TrueType;

use FontLib\AdobeFontMetrics;
use FontLib\Font;
use FontLib\BinaryStream;
use FontLib\Table\Table;
use FontLib\Table\DirectoryEntry;
use FontLib\Table\Type\glyf;
use FontLib\Table\Type\name;
use FontLib\Table\Type\nameRecord;

/**
 * TrueType font file.
 *
 * @package php-font-lib
 */
class File extends BinaryStream
{
    /**
     * @var Header
     */
    public $header;

    private $tableOffset = 0; // Used for TTC

    private static $raw = false;

    protected array $directory = [];
    protected array $data = [];

    protected array $glyph_subset = [];

    public function __construct() {
        $this->header = new Header($this);
    }

    // ... rest of the code ...
}
