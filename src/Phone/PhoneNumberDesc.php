<?php

namespace Phone;

class PhoneNumberDesc {

	private $hasNationalNumberPattern;
	private $nationalNumberPattern_ = "";

	public function hasNationalNumberPattern() {
		return $this->hasNationalNumberPattern;
	}

	public function getNationalNumberPattern() {
		return $this->nationalNumberPattern_;
	}

	public function setNationalNumberPattern($value) {
		$this->hasNationalNumberPattern = true;
		$this->nationalNumberPattern_ = $value;
		return $this;
	}

	private $hasPossibleNumberPattern;
	private $possibleNumberPattern_ = "";

	public function hasPossibleNumberPattern() {
		return $this->hasPossibleNumberPattern;
	}

	public function getPossibleNumberPattern() {
		return $this->possibleNumberPattern_;
	}

	public function setPossibleNumberPattern($value) {
		$this->hasPossibleNumberPattern = true;
		$this->possibleNumberPattern_ = $value;
		return $this;
	}

	private $hasExampleNumber;
	private $exampleNumber_ = "";

	public function hasExampleNumber() {
		return $this->hasExampleNumber;
	}

	public function getExampleNumber() {
		return $this->exampleNumber_;
	}

	public function setExampleNumber($value) {
		$this->hasExampleNumber = true;
		$this->exampleNumber_ = $value;
		return $this;
	}

	public function mergeFrom(PhoneNumberDesc $other) {
		if ($other->hasNationalNumberPattern()) {
			$this->setNationalNumberPattern($other->getNationalNumberPattern());
		}
		if ($other->hasPossibleNumberPattern()) {
			$this->setPossibleNumberPattern($other->getPossibleNumberPattern());
		}
		if ($other->hasExampleNumber()) {
			$this->setExampleNumber($other->getExampleNumber());
		}
		return $this;
	}

	public function exactlySameAs(PhoneNumberDesc $other) {
		return $this->nationalNumberPattern_ === $other->nationalNumberPattern_ &&
				$this->possibleNumberPattern_ === $other->possibleNumberPattern_ &&
				$this->exampleNumber_ === $other->exampleNumber_;
	}

	public function toArray() {
		return array(
			'NationalNumberPattern' => $this->getNationalNumberPattern(),
			'PossibleNumberPattern' => $this->getPossibleNumberPattern(),
			'ExampleNumber' => $this->getExampleNumber(),
		);
	}

	public function fromArray(array $input) {
		if (isset($input['NationalNumberPattern'])) {
			$this->setNationalNumberPattern($input['NationalNumberPattern']);
		}
		if (isset($input['PossibleNumberPattern'])) {
			$this->setPossibleNumberPattern($input['PossibleNumberPattern']);
		}
		if (isset($input['ExampleNumber'])) {
			$this->setExampleNumber($input['ExampleNumber']);
		}
		return $this;
	}

}
