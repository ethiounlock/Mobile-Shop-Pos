<?php

class HTML5_Tokenizer {
    /**
     * @var HTML5_InputStream
     *
     * Points to an InputStream object.
     */
    protected $stream;

    /**
     * @var HTML5_TreeBuilder
     *
     * Tree builder that the tokenizer emits token to.
     */
    private $tree;

    /**
     * @var int
     *
     * Current content model we are parsing as.
     */
    protected $content_model;

    /**
     * Current token that is being built, but not yet emitted. Also
     * is the last token emitted, if applicable.
     */
    protected $token;

    // These are constants describing the content model
    const PCDATA    = 0;
    const RCDATA    = 1;
    const CDATA     = 2;
    const PLAINTEXT = 3;

    // These are constants describing tokens
    const DOCTYPE        = 0;
    const STARTTAG       = 1;
    const ENDTAG         = 2;
    const COMMENT        = 3;
    const CHARACTER      = 4;
    const SPACECHARACTER = 5;
    const EOF            = 6;
    const PARSEERROR     = 7;

    // These are constants representing bunches of characters.
    const ALPHA       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const UPPER_ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const LOWER_ALPHA = 'abcdefghijklmnopqrstuvwxyz';
    const DIGIT       = '0123456789';
    const HEX         = '0123456789ABCDEFabcdef';
    const WHITESPACE  = "\t\n\x0c ";

    /**
     * @param $data | Data to parse
     * @param HTML5_TreeBuilder|null $builder
     */
    public function __construct($data, $builder = null) {
        $this->stream = new HTML5_InputStream($data);
        if (!$builder) {
            $this->tree = new HTML5_TreeBuilder;
        } else {
            $this->tree = $builder;
        }
        $this->content_model = self::PCDATA;
    }

    /**
     * @param null $context
     */
    public function parseFragment($context = null) {
        $this->tree->setupContext($context);
        if ($this->tree->content_model) {
            $this->content_model = $this->tree->content_model;
            $this->tree->content_model = null;
        }
        $this->parse();
    }

    // XXX maybe convert this into an iterator? regardless, this function
    // and the save function should go into a Parser facade of some sort
    /**
     * Performs the actual parsing of the document.
     */
    public function parse() {
        // Current state
        $state = 'data';
        // This is used to avoid having to have look-behind in the data state.
        $lastFourChars = '';
        /**
         * Escape flag as specified by the HTML5 specification: "used to
         * control the behavior of the tokeniser. It is either true or
         * false, and initially must be set to the false state."
         */
        $escape = false;
        //echo "\n\n";
        while($state !== null) {
            //echo $state . ' ';
            //switch ($this->content_model) {
            //    case self::PCDATA: echo 'PCDATA'; break;
            //    case self::RCDATA: echo 'RCDATA'; break;
            //    case self::CDATA: echo 'CDATA'; break;
            //    case self::PLAINTEXT: echo 'PLAINTEXT'; break;
            //}
            //if ($escape) echo " escape";
            //echo "\n";

            switch($state) {
                case 'data':

                    /* Consume the next input character */
                    $char = $this->stream->char();
                    $lastFourChars .= $char;
                    if (strlen($lastFourChars) > 4) {
                        $lastFourChars = substr($lastFourChars, -4);
                    }

                    $hyp_cond =
                        !$escape &&
                        (
                            $this->content_model === self::RCDATA ||
                            $this->content_model === self::CDATA
                        );
                    $amp_cond =
                        !$escape &&
                        (
                            $this->content_model === self::PCDATA ||
                            $this->content_model === self::RCDATA
                        );
                    $lt_cond =
                        $this->content_model === self::PCDATA ||
                        (
                            (
                                $this->content_model === self::RCDATA ||
                                $this->content_model === self::CDATA
                             ) &&
                             !$escape
                        );
                    $gt_cond =
                        $escape &&
                        (
                            $this->content_model === self::RCDATA ||
                            $this->content_model === self::CDATA
                        );

                    if ($char === '&' && $amp_cond === true) {
                        /* U+0026 AMPERSAND (&)
                        When the content model flag is set to one of the PCDATA or RCDATA
                        states and the escape flag is false: switch to the
                        character reference data state. Otherwise: treat it as per
                        the "anything else" entry below. */
                        $state = 'character reference data';

                    } elseif (
                        $char === '-' &&
                        $hyp_cond === true &&
                        $lastFourChars === '<!--'
                    )
