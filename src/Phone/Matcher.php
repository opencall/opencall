<?php

namespace Phone;

class Matcher {
	/**
		 * @var string
		 */
	private $pattern;

	/**
		 * @var string
		 */
	private $subject;

	/**
		 * @var array
		 */
	private $groups = array();

	/**
		 * @param $pattern string
		 * @param $subject string
		 */
	public function __construct($pattern, $subject)
	{
		$this->pattern = $pattern;
		$this->subject = $subject;
	}

	/**
	 * @return bool
	 */
	public function matches() {
		return preg_match('/^(?:' . str_replace('/', '\/', $this->pattern) . ')$/', $this->subject, $this->groups, PREG_OFFSET_CAPTURE) > 0;
	}

	/**
	 * @return bool
	 */
	public function lookingAt() {
		$this->fullPatternMatchesNumber = preg_match_all('/^(?:' . str_replace('/', '\/', $this->pattern) . ')/', $this->subject, $this->groups, PREG_OFFSET_CAPTURE);
		return $this->fullPatternMatchesNumber > 0;
	}

	/**
	 * @return bool
	 */
	public function find() {
		return preg_match('/(?:' . str_replace('/', '\/', $this->pattern) . ')/', $this->subject, $this->groups, PREG_OFFSET_CAPTURE) > 0;
	}


	/**
	 * @return int
	 */
	public function groupCount() {
		return count($this->groups);
	}

	public function group($group = NULL) {
		return $this->groups[$group - 1][0];
	}

	/**
	 * @return int
	 */
	public function end() {
		$lastGroup = $this->groups[$this->fullPatternMatchesNumber - 1][0];
		return $lastGroup[1] + strlen($lastGroup[0]);
	}

	public function replaceFirst($replacement) {
		return preg_replace('/' . str_replace('/', '\/', $this->pattern) . '/', $replacement, $this->subject, 1);
	}

	public function replaceAll($replacement) {
		return preg_replace('/' . str_replace('/', '\/', $this->pattern) . '/', $replacement, $this->subject);
	}

	public function reset($input = "") {
		$this->subject = $input;
		return $this;
	}
}
