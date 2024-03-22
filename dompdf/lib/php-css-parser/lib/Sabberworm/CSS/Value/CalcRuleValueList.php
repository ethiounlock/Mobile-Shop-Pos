<?php

namespace Sabberworm\CSS\Value;

/**
 * A list of calc() rule values.
 */
class CalcRuleValueList extends RuleValueList
{
    /**
     * Constructs a new CalcRuleValueList instance.
     *
     * @param int $iLineNo The line number where this value list was defined.
     */
    public function __construct(int $iLineNo = 0) {
        parent::__construct([], ',', $iLineNo);
    }

    /**
     * Renders this value list as a string.
     *
     * @param \Sabberworm\CSS\OutputFormat $oOutputFormat The output format to use.
     *
     * @return string The rendered value list.
     */
    public function render(\Sabberworm\CSS\OutputFormat $oOutputFormat): string {
        return $oOutputFormat->implode(' ', $this->aComponents);
    }
}
