<?php

namespace Sabberworm\CSS;

use Sabberworm\CSS\Rule\Rule;

/**
 * Parser settings class.
 *
 * Configure parser behaviour here.
 */
class Settings
{
    /**
     * @var bool Multi-byte string support. If true (mbstring extension must be enabled), will use (slower) mb_strlen, mb_convert_case, mb_substr and mb_strpos functions. Otherwise, the normal (ASCII-Only) functions will be used.
     */
    private bool $bMultibyteSupport;

    /**
     * @var string The default charset for the CSS if no `@charset` rule is found. Defaults to utf-8.
     */
    private string $sDefaultCharset = 'utf-8';

    /**
     * @var bool Lenient parsing. When used (which is true by default), the parser will not choke on unexpected tokens but simply ignore them.
     */
    private bool $bLenientParsing = true;

    /**
     * Settings constructor.
     */
    private function __construct()
    {
        $this->bMultibyteSupport = extension_loaded('mbstring');
    }

    /**
     * Create a new Settings instance.
     *
     * @return static
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Set multibyte support.
     *
     * @param bool $bMultibyteSupport
     *
     * @return static
     */
    public function withMultibyteSupport(bool $bMultibyteSupport = true): self

