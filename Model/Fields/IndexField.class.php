<?php
require_once dirname(__FILE__) . '/NumField.class.php';
require_once dirname(__FILE__) . '/../PersistentCollection.class.php';

class IndexField extends NumField {
	var $collection;
	var $nullValue;
	var $target = null;
	var $buffered_target = null;

	function IndexField($name, $isIndex=true, $dataType='__NoType', $nullValue='') {
		if (!is_array($isIndex)) {
			$ps = array('null_value'=>$nullValue,
						'type'=>$dataType,
						'is_index'=>$isIndex,
						'fieldName'=>$name);
			parent :: NumField($ps);
		} else {
			parent :: NumField($name, $isIndex);
		}
	}
	function createInstance($params){
		parent::createInstance($params);
		$this->nullValue = & $params['null_value'];
		$this->collection = & new PersistentCollection($params['type']);
	}
	function & visit(& $obj) {
		return $obj->visitedIndexField($this);
	}

	function & obj() {
		return $this->getTarget();
	}

	function setTarget(& $target) {
		$this->setValue($target->getIdOfClass($this->collection->dataType));
		$this->buffered_target =& $target;
	}

	function & getTarget() {
		if (!$this->buffered_target) {
			$this->buffered_target =& $this->collection->getObj($this->getValue());
		}
		return $this->buffered_target;
	}

	function viewValue() {
		$obj = & $this->obj();
		if ($obj){
			return $obj->indexValues();
		} else {
			return '';
		}
	}

	function setValue($value) {
		parent::setValue($value);
		$n = null;
		$this->buffered_target =& $n;
	}

	function getValue(){
		$v = parent::getValue();
		if ($v!=null){
			return $v;
		} else {
			return 0;
		}
	}
	function flushChanges() {
		parent::flushChanges();
		$this->buffered_target =& $this->target;
	}

	function commitChanges() {
		parent::commitChanges();
		$this->target =& $this->getTarget();
	}
}

?>