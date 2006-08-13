<?php
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
	function add(&$elem){
		$m =& $this->createElement();
		$f1 = $this->creationParams['joinField'];
		$m->$f1->setTarget($elem);
		return $m->save();
	}
	function &createElement(){
		$m =& new PersistentObject();
		$params = $this->creationParams;
		$f1 = $params['joinFieldOwn'];
		$f2 = $params['joinField'];
		$m->addField(new IndexField(array('fieldName'=>$f1,'type'=>getClass($this->owner))));
		$m->addField(new IndexField(array('fieldName'=>$f2,'type'=>$params['type'])));
		$m->table = $params['joinTable'];
		$m->$f1->setTarget($this->owner);
		return $m;
	}
	function remove(&$elem){
		$m =& $this->createElement();
		$params = $this->creationParams;
		$sql = 'DELETE FROM '.$params['joinTable']. ' WHERE '. $params['joinField'].'=' .$elem->getIdOfClass($params['joinField']).' AND ' .$params['joinFieldOwn'] .'='.$this->owner->getId();
		$db =& DB::instance();
		return $db->query($sql);
	}
	function defaultValues($params) {
		$v = array (
			'fieldName' => $params['type'] . $params['reverseField'],
			'joinTable' => $params['type'],
			'joinFieldOwn' => strtolower(getClass($this->owner)),
			'joinField' => strtolower($params['type']));

		return array_merge($v,parent :: defaultValues(array_merge($params,$v)));
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
		// Note: we should be using CollectionIterator instead of Collections
		// If we modify this collection with setCondition, then this will not work
		// This field collection should be inmutable
		return $this->collection->isEmpty();
		/*
		$arr = & $this->collection->elements();
		$can = count($arr) == 0;
		if (!$can)
			trace("The " . $this->colName . " collection is not empty<BR>\n");
		return $can;*/
	}
}
?>
