<?php

declare(strict_types=1);

namespace Dompdf\FrameReflower;

use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\FrameDecorator\Page as PageFrameDecorator;

/**
 * @package dompdf
 */
class Page extends AbstractFrameReflower
{
    /**
     * @var array
     */
    private array $_callbacks;

    /**
     * @var \Dompdf\Canvas|null
     */
    private ?\Dompdf\Canvas $_canvas;

    /**
     * Page constructor.
     * @param PageFrameDecorator $frame
     */
    public function __construct(PageFrameDecorator $frame)
    {
        parent::__construct($frame);
    }

    /**
     * @param Frame $frame
     * @param int $page_number
     */
    public function applyPageStyle(Frame $frame, int $page_number): void
    {
        // ...
    }

    /**
     * Paged layout:
     * http://www.w3.org/TR/CSS21/page.html
     *
     * @param BlockFrameDecorator|null $block
     */
    public function reflow(?BlockFrameDecorator $block = null): void
    {
        // ...
    }

    /**
     * Check for callbacks that need to be performed when a given event
     * gets triggered on a page
     *
     * @param string $event
     * @param Frame $frame
     */
    protected function _checkCallbacks(string $event, Frame $frame): void
    {
        // ...
    }
}

<?php

[phpstan]
includeFile = vendor/autoload.php

parameters =
    cacheDir = /path/to/cache
    cache = array(
        'dompdf' => '\\Dompdf\\Cache',
    )

appendToBlacklist =
    - *\Tests\*
    - *\tests\*
    - *Fixture*
    - *_support*
    - *_data*

