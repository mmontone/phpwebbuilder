<?php
require_once dirname(__FILE__) . '/DataField.class.php';

class CollectionField extends DataField {
	var $collection;
	var $fieldname;

	function CollectionField($name, $dataType = array ()) {
		if (is_array($dataType)) {
			$type = $dataType['type'];
			parent :: DataField($type . $name, $dataType);
			$this->collection = & new PersistentCollection($type);
		}
		else {
			parent :: DataField($dataType . $name, FALSE);
			$this->collection = & new PersistentCollection($dataType);
		}
		$this->fieldname = $name;
		$this->collection->conditions[$this->fieldname] = array (
			"=",
			"0"
		);
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
	function canDelete() {
		$arr = & $this->collection->elements();
		$can = count($arr) == 0;
		if (!$can)
			trace("The " . $this->colName . " collection is not empty<BR>\n");
		return $can;
	}
}
?>
