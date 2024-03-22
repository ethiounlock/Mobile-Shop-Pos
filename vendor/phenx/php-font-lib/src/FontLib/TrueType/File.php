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

    private $tableOffset; // Used for TTC

    private static $raw = false;

    protected $directory = [];
    protected $data = [];

    protected $glyph_subset = [];

    public $glyph_all = [];

    // ... rest of the code
}
