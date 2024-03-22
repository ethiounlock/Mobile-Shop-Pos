<?php

declare(strict_types=1);

namespace Dompdf;

use Dompdf\Frame;

final class JavascriptEmbedder
{
    /**
     * @var Dompdf
     */
    private $dompdf;

    /**
     * JavascriptEmbedder constructor.
     *
     * @param Dompdf $dompdf
     */
    public function __construct(Dompdf $dompdf)
    {
        $this->dompdf = $dompdf;
    }

    /**
     * @param string $script
     */
    public function insert(string $script): void
    {
        $this->dompdf->getCanvas()->javascript($script);
    }

    /**
     * @param Frame $frame
     *
     * @throws \RuntimeException
     */
    public function render(Frame $frame): void
    {
        if (!$this->dompdf->getOptions()->getIsJavascriptEnabled()) {
            return;
        }

        $nodeValue = $frame->getNodeValue();
        if (!\is_string($nodeValue)) {
            throw new \RuntimeException('Node value must be a string.');
        }

        $this->insert($nodeValue);
    }

    const JS = 'javascript';
}
