<?php

namespace Sabberworm\CSS\Value;

/**
 * CSSFunction class representing a CSS function.
 */
final class CSSFunction extends ValueList {

	/**
	 * @var string The name of the CSS function.
	 */
	protected $sName;

	/**
	 * CSSFunction constructor.
	 *
	 * @param string          $sName     The name of the CSS function.
	 * @param array<Value>   $aArguments The arguments of the CSS function.
	 * @param int             $iLineNo   The line number where the CSS function is defined.
	 */
	final public function __construct(string $sName, array $aArguments, int $iLineNo = 0) {
		if ($aArguments instanceof RuleValueList) {
			$aArguments = $aArguments->getListComponents();
		}
		$this->sName = $sName;
		$this->iLineNo = $iLineNo;
		parent::__construct($aArguments, $iLineNo);
	}

	/**
	 * Get the name of the CSS function.
	 *
	 * @return string The name of the CSS function.
	 */
	public function getName(): string {
		return $this->sName;
	}

	/**
	 * Set the
