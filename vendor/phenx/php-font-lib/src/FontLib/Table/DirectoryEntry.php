<?php
/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace FontLib\Table;

use FontLib\TrueType\File;
use FontLib\Font;
use FontLib\BinaryStream;

/**
 * Generic Font table directory entry.
 *
 * @package php-font-lib
 */
class DirectoryEntry extends BinaryStream
{
    /**
     * @var File
     */
    protected $font;

    /**
     * @var Table
     */
    protected $font_table;

    public $entryLength = 4;

    public $tag;
    public $checksum;
    public $offset;
    public $length;

    protected $origF;

    /**
     * Compute the checksum of the given data.
     *
     * @param string $data
     *
     * @return int
     */
    static function computeChecksum($data)
    {
        // ...
    }

    /**
     * DirectoryEntry constructor.
     *
     * @param File $font
     */
    function __construct(File $font)
    {
        $this->font = $font;
        $this->f    = $font->f;
    }

    /**
     * Parse the directory entry.
     *
     * @throws \RuntimeException
     */
    function parse()
    {
        $this->tag = $this->font->read(4);

        if ($this->tag === false) {
            throw new \RuntimeException('Failed to read tag.');
        }
    }

    /**
     * Open the file.
     *
     * @param string $filename
     * @param int    $mode
     */
    function open($filename, $mode = self::modeRead)
    {
        $this->f = fopen($filename, $mode);

        if ($this->f === false) {
            throw new \RuntimeException('Failed to open file.');
        }
    }

    /**
     * Set the font table.
     *
     * @param Table $font_table
     */
    function setTable(Table $font_table)
    {
        $this->font_table = $font_table;
    }

    /**
     * Encode the directory entry.
     *
     * @throws \RuntimeException
     */
    function encode()
    {
        Font::d("\n==== {$this->tag} ====");
        //Font::d("Entry offset  = $entry_offset");

        $data = $this->font_table;
        $font = $this->font;

        $table_offset = $font->pos();
        $this->offset = $table_offset;
        $table_length = $data->encode();

        if ($table_length === false) {
            throw new \RuntimeException('Failed to encode table.');
        }

        $font->seek($table_offset);
        $table_data = $font->read($table_length);

        $font->seek($this->offset);

        $font->write($this->tag, 4);
        $font->writeUInt32(self::computeChecksum($table_data));
        $font->writeUInt32($table_offset);
        $font->writeUInt32($table
