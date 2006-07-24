<?php
require_once dirname(__FILE__) . '/DataField.class.php';

class CollectionField extends DataField {
	var $collection;
	var $fieldname;

	function CollectionField($name, $dataType = array ()) {
		if (is_array($dataType)) {
			$dataType['reverseField'] = $name;
			parent :: DataField($dataType);
		} else if (is_array($name)){
			parent :: DataField($name);
		} else {
			parent :: DataField(array('reverseField'=>$name, 'type'=>$dataType));
		}
	}
	function createInstance($params){
		parent::createInstance($params);
		$this->fieldname = $params['reverseField'];
		$this->collection = & new PersistentCollection($params['type']);
		$this->collection->conditions[$this->fieldname] = array (
			"=",
			"-1"
		);
	}
	function defaultValues($params){
		return array_merge(array('fieldName'=>$params['type'].$params['reverseField'])
				,parent::defaultValues($params));
	}
	function fieldName() {}
	function & visit(& $obj) {
		return $obj->visitedCollectionField($this);
	}

	function setID($id) {
		$this->setValue($id);
		$this->collection->conditions[$this->fieldname] = array (
			"=",
			$id
		);
	}
	function SQLvalue() {}
	function updateString() {}
	function loadFrom(&$reg) {
		return true;
	}
	function & objects() {
		return $this->elements();
	}
	function &elements() {
		return $this->collection->elements();
	}

	function setValue($value) {
		// Don't register modification
		$this->buffered_value = $value;
	}

	function canDelete() {
		$arr = & $this->collection->elements();
		$can = count($arr) == 0;
		if (!$can)
			trace("The " . $this->colName . " collection is not empty<BR>\n");
		return $can;
	}
}
?>
