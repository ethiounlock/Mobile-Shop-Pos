<?php
/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace FontLib\OpenType;

use FontLib\TrueType\File as TrueTypeFile;

/**
 * Open Type font, the same as a TrueType one.
 *
 * This class represents OpenType font files, which are a superset of TrueType fonts.
 * It extends the `\FontLib\TrueType\File` class to provide additional functionality
 * specific to OpenType fonts.
 *
 * @package php-font-lib
 */
class File extends TrueTypeFile {
  const FILE_FORMAT_OPEN_TYPE = 'OpenType';

  /**
   * Create a new OpenType font file.
   *
   * @param string $filename The path to the font file.
   */
  public function __construct($filename) {
    parent::__construct($filename);
    $this->file_format = self::FILE_FORMAT_OPEN_TYPE;
  }

  /**
   * Get the name of the file format.
   *
   * @return string The name of the file format.
   */
  public function getFormatName() {
    return $this->file_format;
  }
}
