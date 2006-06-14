<?php
require_once dirname(__FILE__) . '/NumField.class.php';
require_once dirname(__FILE__) . '/../PersistentCollection.class.php';

class IndexField extends NumField {
	var $collection;
	var $nullValue;

	function IndexField($name, $isIndex=true, $dataType='__NoType', $nullValue='') {
		if (is_array($isIndex)) {
			parent :: NumField($name, $isIndex);
			$this->collection = & new PersistentCollection($isIndex['type']);
			$this->nullValue = & $isIndex['null_value'];
		}
		else {
			parent :: NumField($name, $isIndex);
			$this->collection = & new PersistentCollection($dataType);
			$this->nullValue = & $nullValue;
		}
	}

	function & visit(& $obj) {
		return $obj->visitedIndexField($this);
	}

	function & obj() {
		return $this->getTarget();
	}

	function setTarget(& $target) {
		$this->setValue($target->getIdOfClass($this->collection->dataType));
	}

	function & getTarget() {
		return $this->collection->getObj($this->getValue());
	}

	function viewValue() {
		$obj = & $this->obj();
		return $obj->indexValues();
	}

	function getValue() {
		if (parent::getValue() == null) {
			return 0;
		}
		else {
			return parent::getValue();
		}
	}
}
?>