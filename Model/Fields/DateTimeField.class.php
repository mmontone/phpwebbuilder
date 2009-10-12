<?php
class DateTimeField extends DataField {
	function DateTimeField($name, $isIndex = null) {
		parent :: DataField($name, $isIndex);
		$this->primSetValue(PWBDateTime :: Now());
		$this->primitiveCommitChanges();
	}
	function primitiveCommitChanges() {
		$this->value = & $this->buffered_value;
		$this->setModified(false);
	}

	function printString() {
		$date = & $this->getValue();
		return $this->primPrintString($this->getName() . ' - ' . $date->printString());
	}

	function viewValue() {
		$value = & $this->getValue();
		return $value->printString();
	}
	function createInstance($params) {
		parent :: createInstance($params);
		$this->primSetValue(new PWBDateTime(''));
	}
	function SQLvalue() {
		$d = & $this->getValue();
		return "'" . $d->printString() . "'" . ", ";
	}
	function loadFrom($reg) {
		$val = @ $reg[$this->sqlName()];
		$this->setDate($val);
	}
	function setDate($val) {
		$this->setValue(new PWBDateTime($val));
	}
	function & validate() {
		$v = & $this->getValue();
		if ((!$v->validateTime()) or (!$v->validateDate())) {
			return new ValidationException(array (
				'message' => Translator :: translate('The time or date are invalid'),
				'content' => & $this
			));
		}
		$f = false;
		return $f;
	}

	function setNow() {
		$this->setValue(PWBDateTime :: now());
	}
	function dateObjectChanged() { //Simulate a normal change of values, the object has changed
		$d = & $this->getValue();
		$this->primSetValue($d);
		$this->buffered_value = & $d;
		$this->registerFieldModification();
	}
	function setValue(& $d) {
		#@typecheck $d : PWBDateTime@#
		$d->addInterestIn('changed', new FunctionObject($this, 'dateObjectChanged'), array (
			'execute on triggering' => true
		));

		if (!($d->isLike($this->buffered_value))) {
			$this->primSetValue($d);
			$this->buffered_value = & $d;
			$this->registerFieldModification();
		}
	}
	function & getValue() {
		return $this->buffered_value;
	}

	function & getDate() {
		return $this->getValue();
	}
}

class DateField extends DateTimeField {
	function format($date) {
		return PWBDateTime :: dateFormat($date);
	}
	function & validate() {
		$v = & $this->getValue();
		if (!$v->validateDate()) {
			$val = & new ValidationException(array (
				'message' => Translator :: translate('The date is invalid'),
				'content' => & $this
			));
			return $val;
		}
		$f = false;
		return $f;
	}
}

class TimeField extends DateTimeField {
	function format($date) {
		return PWBDateTime :: timeFormat($date);
	}
	function & validate() {
		$v = & $this->getValue();
		if (!$v->validateTime()) {
			return new ValidationException(array (
				'message' => Translator :: translate('The time is invalid'),
				'content' => & $this
			));
		}
		$f = false;
		return $f;
	}
}
?>