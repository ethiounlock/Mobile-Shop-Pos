<?php

namespace Dompdf;

class Options
{
    private string $rootDir;
    private string $tempDir;
    private string $fontDir;
    private string $fontCache;
    private array $chroot;
    private string $logOutputFile;
    private string $defaultMediaType = "screen";
    private string $defaultPaperSize = "letter";
    private string $defaultPaperOrientation = "portrait";
    private string $defaultFont = "serif";
    private int $dpi = 96;
    private float $fontHeightRatio = 1.1;
    private bool $isPhpEnabled = false;
    private bool $isRemoteEnabled = false;
    private bool $isJavascriptEnabled = true;
    private bool $isHtml5ParserEnabled = false;
    private bool $isFontSubsettingEnabled = true;
    private bool $debugPng = false;
    private bool $debugKeepTemp = false;
    private bool $debugCss = false;
    private bool $debugLayout = false;
    private bool $debugLayoutLines = true;
    private bool $debugLayoutBlocks = true;
    private bool $debugLayoutInline = true;
    private bool $debugLayoutPaddingBox = true;
    private string $pdfBackend = "CPDF";
    private string $pdflibLicense = "";

    public function __construct(array $attributes = null)
    {
        $rootDir = realpath(__DIR__ . "/../");
        $this->setRootDir($rootDir);
        $this->setTempDir(sys_get_temp_dir());
        $this->setFontDir($rootDir . "/lib/fonts");
        $this->setFontCache($this->getFontDir());
        $this->setLogOutputFile($this->getTempDir() . "/log.htm");

        if ($attributes !== null) {
            $this->set($attributes);
        }
    }

    public function set(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        return $this;
    }

    public function get(string $key): mixed
    {
        $methodName = 'get' . ucfirst($key);

        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return null;
    }

    public function setRootDir(string $rootDir): self
    {
        if (!is_dir($rootDir)) {
            throw new \InvalidArgumentException("The root directory does not exist.");
        }

        $this->rootDir = $rootDir;

        return $this;
    }

    public function getRootDir(): string
    {
        return $this->rootDir;

