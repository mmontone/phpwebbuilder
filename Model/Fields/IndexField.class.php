<?php

class IndexField extends NumField {
	var $collection;
	var $nullValue;
	var $target = null;
	var $buffered_target = null;
	var $datatype;

	function IndexField($name, $isIndex=null, $dataType=null, $nullValue=null) {
		if (!is_array($isIndex) && !is_array($name)) {
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
		$this->datatype =& $params['type'];
	}
	function & visit(& $obj) {
		return $obj->visitedIndexField($this);
	}

	function & obj() {
		return $this->getTarget();
	}

	function getDataType() {
		return $this->datatype;
	}

	function setTarget(& $target) {
		#@typecheck $target:PersistentObject@#
        if (($this->buffered_target == null) or !($this->buffered_target->is($target))) {
            $this->buffered_target =& $target;
    		$n = null;
    		$this->buffered_value =& $n;
    		$this->triggerEvent('changed', $this);
        }
	}

	function getTargetId() {
		//return $this->buffered_target->getIdOfClass($this->collection->dataType);
		return $this->buffered_target->getIdOfClass($this->datatype);
	}

	function & getTarget() {
		if (!$this->buffered_target) {
			$this->buffered_target =& $this->loadTarget();
		}
		return $this->buffered_target;
	}

	function &loadTarget() {
		//return $this->collection->getObj($this->getValue());
		$o =& PersistentObject::getWithId($this->datatype, $this->getValue());
		return $o;
	}

	/*function prepareToSave(){
		if ($this->buffered_target!=null){
			$this->buffered_target->save();
		}
	}*/
	function viewValue() {
		$obj = & $this->obj();
		if ($obj){
			return $obj->printString();
		} else {
			return '';
		}
	}

	function setValue($value) {
		parent::setValue((integer)$value);

		$n = null;
		$this->buffered_target =& $n;
	}

	function getValue() {
		if ($this->buffered_target != null) {
			$this->buffered_value = $this->getTargetId();
		}

		$v = parent::getValue();
		if ($v!=null){
			return $v;
		} else {
			return 0;
		}
	}

	/*
	function flushChanges() {
		parent::flushChanges();
		if ((is_object($this->buffered_target)) and (!($this->buffered_target->is($this->target)))) {
			$this->buffered_target =& $this->target;
			$this->triggerEvent('changed', $no_params = null);
		}
	}
	*/

	function commitChanges() {
		parent::commitChanges();
		$this->target =& $this->buffered_target;
	}

	function isEmpty() {
		return $this->getTarget() == null;
	}

	function &asValueModel() {
		return new AspectAdaptor($this, 'Target');
	}

    #@gencheck
    function SQLvalue() {
        if ($this->getValue() == 0) {
        	print_backtrace('Warning!!: Index field sql value is 0. Field name: ' . $this->varName);
        }
        return parent::SQLvalue();
    }
    //@#
}

?>