<?php
require_once dirname(__FILE__) . '/NumField.class.php';
require_once dirname(__FILE__) . '/../PersistentCollection.class.php';

class IndexField extends NumField {
	var $collection;
	var $nullValue;

	function IndexField($name, $isIndex, $dataType, $nullValue = "") {
		if (is_array($isIndex)) {
			parent :: Numfield($name, $isIndex['isIndex']);
			$this->collection = & new PersistentCollection($isIndex['type']);
			$this->nullValue = & $isIndex['null_value'];
		}
		else {
			parent :: Numfield($name, $isIndex);
			$this->collection = & new PersistentCollection($dataType);
			$this->nullValue = & $nullValue;
		}
	}

	function & visit(& $obj) {
		return $obj->visitedIndexField($this);
	}

	function & obj() {
		return $this->collection->getObj($this->getValue());
	}

	function setTarget(& $target) {
		$this->setValue($target->getID());
	}

	function & getTarget() {
		return $this->obj();
	}

	function viewValue() {
		$obj = & $this->obj();
		return $obj->indexValues();
	}

	function getValue() {
		if ($this->value == null) {
			return 0;
		}
		else {
			return $this->value;
		}
	}
}
?>