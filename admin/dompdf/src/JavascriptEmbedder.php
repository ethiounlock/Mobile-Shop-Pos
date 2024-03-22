<?php
declare(strict_types=1);

namespace Dompdf;

/**
 * @package dompdf
 * @link    http://dompdf.github.com/
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
final class JavascriptEmbedder
{
    /**
     * @var Dompdf
     */
    private $_dompdf;

    /**
     * JavascriptEmbedder constructor.
     *
     * @param Dompdf $dompdf
     */
    public function __construct(Dompdf $dompdf)
    {
        $this->_dompdf = $dompdf;
    }

    /**
     * Inserts a JavaScript script into the PDF document.
     *
     * @param string $script
     *
     * @throws \InvalidArgumentException if the script is not a string.
     */
    public function insert(string $script): void
    {
        if (!is_string($script)) {
            throw new \InvalidArgumentException('The script must be a string.');
        }

        $this->_dompdf->getCanvas()->javascript($script);
    }

    /**
     * Renders the JavaScript script for the
