<?php
require_once dirname(__FILE__) . '/DataField.class.php';

class CollectionField extends DataField {
	var $collection;
	var $fieldname;

	function CollectionField($name, $dataType = array ()) {
		if (is_array($name)) {
			parent :: DataField($name);
		} else
			if (is_array($dataType)) {
				$dataType['reverseField'] = $name;
				parent :: DataField($dataType);
			} else {
				parent :: DataField(array (
					'reverseField' => $name,
					'type' => $dataType
				));
			}
	}
	function createInstance($params) {
		parent :: createInstance($params);
		if ($params['reverseField']==null) {
			$this->collection = & new JoinedPersistentCollection($params['type'], $params['joinTable'], $params['joinField']);
			$this->creationParams['reverseField'] = $params['joinTable'].'.'.$params['joinFieldOwn'];
		} else {
			$this->collection = & new PersistentCollection($params['type']);
		}
		$this->collection->conditions[$this->creationParams['reverseField']] = array (
			'=',
			'-1'
		);
	}
	function defaultValues($params) {
		return array_merge(array (
			'fieldName' => $params['type'] . $params['reverseField'],
			'joinTable' => $params['type'],
			'joinFieldOwn' => strtolower(getClass($this->owner)),
			'joinField' => strtolower($params['type']),

		), parent :: defaultValues($params));
	}
	function fieldName() {
	}
	function & visit(& $obj) {
		return $obj->visitedCollectionField($this);
	}

	function setID($id) {
		$this->setValue($id);
		$this->collection->conditions[$this->creationParams['reverseField']] = array (
			'=',
			$id
		);
	}
	function SQLvalue() {
	}
	function updateString() {
	}
	function loadFrom(& $reg) {
		return true;
	}
	function & objects() {
		return $this->elements();
	}
	function & elements() {
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
