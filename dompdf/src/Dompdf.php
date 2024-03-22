<?php

declare(strict_types=1);

namespace Dompdf;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use SplFileInfo;

class Dompdf
{
    private string $version = 'dompdf';

    private DOMDocument $dom;

    private FrameTree $tree;

    private Stylesheet $css;

    private Canvas $canvas;

    private string $paperSize = 'a4';

    private string $paperOrientation = 'portrait';

    private array $callbacks = [];

    private string $cacheId;

    private string $baseHost = '';

    private string $basePath = '';

    private string $protocol;

    private ?resource $httpContext;

    private int $startTime;

    private string $systemLocale;

    private string $mbstringEncoding;

    private string $pcreJit;

    private string $defaultView = 'Fit';

    private array $defaultViewOptions = [];

    private bool $quirksmode = false;

    private array $allowedProtocols = [null, '', 'file://', 'http://', 'https://'];

    private array $allowedLocalFileExtensions = ['htm', 'html'];

    public function __construct(array $options = [])
    {
        $this->setOptions($options);

        $this->paperSize = $this->options->getDefaultPaperSize();
        $this->paperOrientation = $this->options->getDefaultPaperOrientation();

        $this->canvas = CanvasFactory::get_instance($this, $this->paperSize, $this->paperOrientation);
        $this->css = new Stylesheet($this);

        $this->restorePhpConfig();
    }

    private function setPhpConfig(): void
    {
        if (sprintf('%.1f', 1.0) !== '1.0') {
            $this->systemLocale = setlocale(LC_NUMERIC, '0');
            setlocale(LC_NUMERIC, 'C');
        }

        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $this->pcreJit = @ini_get('pcre.jit');
            @ini_set('pcre.jit', '0');
        }

        $this->mbstringEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');
    }

    private function restorePhpConfig(): void
    {
        if (!empty($this->systemLocale)) {
            setlocale(LC_NUMERIC, $this->systemLocale);
            $this->systemLocale = null;
        }

        if (!empty($this->pcreJit)) {
            @ini_set('pcre.jit', $this->pcreJit);
            $this->pcreJit = null;
        }

        if (!empty($this->mbstringEncoding)) {
            mb_internal_encoding($this->mbstringEncoding);
            $this->mbstringEncoding = null;
        }
    }

    public function loadHtmlFile(string $file, string $encoding = null): void
    {
        $this->setPhpConfig();

        [$this->protocol, $this->baseHost, $this->basePath] = Helpers::explode_url($file);

        $uri = Helpers::build_url($this->protocol, $this->baseHost, $this->basePath, $file);

        if (!in_array($this->protocol, $this->allowedProtocols)) {
            throw new Exception("Permission denied on $file. The communication protocol is not supported.");
        }

        if (!$this->options->isRemoteEnabled() && ($this->protocol !== '' && $this->protocol !== 'file://')) {
            throw new Exception("Remote file requested, but remote file download is disabled.");
        }

        $realfile = realpath($uri);

        if ($this->protocol === '' || $this->protocol === 'file://') {
            $ext = strtolower(pathinfo($realfile, PATHINFO_EXTENSION));

            if (!in_array($ext, $this->allowedLocalFileExtensions)) {
                throw new Exception("Permission denied on $file. This file extension is forbidden");
            }

            if (!$realfile) {
                throw new Exception("File '$file' not found.");
            }

            $uri = $realfile;
        }

        [$contents, $http_response_header] = Helpers::getFileContent($uri, $this->httpContext);

        if (empty($contents)) {
            throw new Exception("File '$file' not found.");
        }

        $encoding = $encoding ?? $this->detectEncoding($contents);

        $this->loadHtml($contents, $encoding);
    }

    private function detectEncoding(string $content): string
    {
        mb_detect_order('auto');
        $encoding = mb_detect_encoding($content, null, true);

        if ($encoding === false) {
            $encoding = 'auto';
        }

        return $encoding;
    }

    public function loadHtml(string $html, string $encoding = null): void
    {
        $this->setPhpConfig();

        if ($encoding === null) {
            $encoding = $this->detectEncoding($html);
        }

        if (in_array(strtoupper($encoding), ['UTF-8', 'UTF8']) === false) {
            $html = mb_convert_encoding($html, 'UTF-8', $encoding);

            //Update encoding after converting
            $encoding = 'UTF-8';
        }

        $metatags = [
            '@<meta\s+
