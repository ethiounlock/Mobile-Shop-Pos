<?php

namespace Dompdf;

class Options
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var string
     */
    private $fontDir;

    /**
     * @var string
     */
    private $fontCache;

    /**
     * @var array
     */
    private $chroot;

    /**
     * @var string
     */
    private $logOutputFile;

    /**
     * @var string
     */
    private $defaultMediaType = "screen";

    /**
     * @var string
     */
    private $defaultPaperSize = "letter";

    /**
     * @var string
     */
    private $defaultPaperOrientation = "portrait";

    /**
     * @var string
     */
    private $defaultFont = "serif";

    /**
     * @var int
     */
    private $dpi = 96;

    /**
     * @var float
     */
    private $fontHeightRatio = 1.1;

    /**
     * @var bool
     */
    private $isPhpEnabled = false;

    /**
     * @var bool
     */
    private $isRemoteEnabled = false;

    /**
     * @var bool
     */
    private $isJavascriptEnabled = true;

    /**
     * @var bool
     */
    private $isHtml5ParserEnabled = false;

    /**
     * @var bool
     */
    private $isFontSubsettingEnabled = true;

    /**
     * @var bool
     */
    private $debugPng = false;

    /**
     * @var bool
     */
    private $debugKeepTemp = false;

    /**
     * @var bool
     */
    private $debugCss = false;

    /**
     * @var bool
     */
    private $debugLayout = false;

    /**
     * @var bool
     */
    private $debugLayoutLines = true;

    /**
     * @var bool
     */
    private $debugLayoutBlocks = true;

    /**
     * @var bool
     */
    private $debugLayoutInline = true;

    /**
     * @var bool
     */
    private $debugLayoutPaddingBox = true;

    /**
     * @var string
     */
    private $pdfBackend = "CPDF";

    /**
     * @var string
     */
    private $pdflibLicense = "";

    public function __construct(array $attributes = [])
    {
        $rootDir = realpath(__DIR__ . "/../");
        $this->setRootDir($rootDir);
        $this->setTempDir(sys_get_temp_dir());
        $this->setFontDir($rootDir . "/lib/fonts");
        $this->setFontCache($this->getFontDir());
        $this->setLogOutputFile($this->getTempDir() . "/log.htm");

        $this->set($attributes);
    }

    public function set(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            switch ($key) {
                case 'tempDir':
                case 'temp_dir':
                    $this->setTempDir($value);
                    break;
                case 'fontDir':
                case 'font_dir':
                    $this->setFontDir($value);
                    break;
                case 'fontCache':
                case 'font_cache':
                    $this->setFontCache($value);
                    break;
                case 'chroot':
                    $this->setChroot($value);
                    break;
                case 'logOutputFile':
                case 'log_output_file':
                    $this->setLogOutputFile($value);
                    break;
                case 'defaultMediaType':
                case 'default_media_type':
                    $this->setDefaultMediaType($value);
                    break;
                case 'defaultPaperSize':
                case 'default_paper_size':
                    $this->setDefaultPaperSize($value);
                    break;
                case 'defaultPaperOrientation':
                case 'default_paper_orientation':
                    $this->setDefaultPaperOrientation($value);
                    break;
                case 'defaultFont':
                case 'default_font':
                    $this->setDefaultFont($value);
                    break;
                case 'dpi':
                    $this->setDpi($value);
                    break;
                case 'fontHeightRatio':
                case 'font_height_ratio':
                    $this->setFontHeightRatio($value);
                    break;
                case 'isPhpEnabled':
                case 'is_php_enabled':
                case 'enable_php':
                    $this->setIsPhpEnabled($value);
                    break;
                case 'isRemoteEnabled':
                case 'is_remote_enabled':
                case 'enable_remote':
                    $this->setIsRemoteEnabled($value);
                    break;
                case 'isJavascriptEnabled':
                case 'is_javascript_enabled':
                case 'enable_javascript':
                    $this->setIsJavascriptEnabled($value);
                    break;
                case 'isHtml5ParserEnabled':

