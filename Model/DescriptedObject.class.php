<?php

class DescriptedObject extends PWBObject {
	var $parent = NULL;

    var $fieldNames = array ();

	var $indexFields = array ();

	var $displayString;

	function DescriptedObject() {
    	parent::PWBObject();
    }

	function commitChanges() {
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->commitChanges();
		}
	}

	function flushChanges() {
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->flushChanges();
		}
	}

	function & createInstance() {
		if ($this->isNotTopClass($this)) {
			$this->setParent($this->create(get_parent_class(getClass($this))));
		}
		$this->basicInitialize();
		return $this;
	}
	function & create($class) {
		return new $class;
	}
	function basicInitialize() {
		$this->addField(new idField("id", FALSE));
		if ($this->isNotTopClass($this)) {
			$this->addField(new superField("super", FALSE));
		}
		$this->displayString = ucfirst(getClass($this));
		if($this->table==''){
			$this->table = getClass($this);
		}

		$this->initialize();
	}
	function initialize(){}
    function updateFields() {
    	$this->table = $this->table_field->getValue();
    	$this->displayString = $this->display_field->getValue();
    }

    function loadFrom(&$reg) {
		if ($this->isNotTopClass($this)){
			$this->parent->loadFrom($reg);
		}
		$ok = true;
		foreach ($this->allFieldNamesThisLevel() as $index) {
			$field = & $this-> $index;
			$ok = $ok and $field->loadFrom($reg);
		}

		if (!$ok) {
			$this->flushChanges();
			return false;
		}
		else {
			$this->commitChanges();
			$this->setID($this->id->getValue());
			$this->existsObject = TRUE;
			return true;
		}
	}

	function isNotTopClass(& $class) {
		return (strcasecmp(get_parent_class($class), 'PersistentObject') != 0 && strcasecmp(get_parent_class($class), 'DescriptedObject') != 0);
	}

    function & allFieldsThisLevel() {
		return $this->fieldsWithNames($this->allFieldNamesThisLevel());
	}
	function & allFields() {
		return $this->fieldsWithNames($this->allFieldNames());
	}
	function & fieldsWithNames($names) {
		$arr = array ();
		foreach ($names as $name) {
			$f =& $this->fieldNamed($name);
			if ($f==null){
				print_backtrace('Object '.getClass($this).' doesn\'t have field '.
					$name.' in '.print_r($names, TRUE));
			}
			$arr[$name] = & $f;
		}
		return $arr;
	}

	function allFieldNames() {
		if ($this->isNotTopClass(getClass($this))) {
			if ($this->parent == null)
				print_backtrace(getClass($this));

			return array_merge($this->parent->allFieldNames(), $this->allFieldNamesThisLevel());
		}
		else {
			return $this->allFieldNamesThisLevel();
		}
	}
	function allIndexFieldNames() {
		if ($this->isNotTopClass(getClass($this))) {
			if ($this->parent == null)
				backtrace();
			return array_merge($this->parent->allIndexFieldNames(), $this->indexFields);
		}
		else {
			return $this->indexFields;
		}
	}
	function & allIndexFields() {
		return $this->fieldsWithNames($this->allIndexFieldNames());
	}
	function allFieldNamesThisLevel() {
		return $this->fieldNames;
	}
	function & fieldNamed($name) {
		$f = & $this-> $name;
		return $f;
	}
	function setField($name, & $field) {
		$this-> $name = & $field;
	}
	function & getField($f) {
		return $this-> $f;
	}
	function & getFields($fs) {
		$arr = array ();
		foreach ($fs as $f) {
			$arr[] = & $this->getField($f);
		}
		return $arr;
	}
	function addField(& $field) {
		$name = $field->colName;
		$this-> $name = & $field;
		$this->fieldNames[$name] = $name;
		if ($field->isIndex) {
			$this->indexFields[$name] = $name;
		}
		$field->owner = & $this;
		//classkit_method_add ( getClass($this), 'get' . $name, '', 'return $this->get' . $name . ';');

		/*$field->addEventListener($this, $a = array (
			'change' => 'fieldChanged'
		));*/
	}

	function fieldChanged(& $field) {
		$this->triggerEvent('fieldChanged', $field);
	}

	function & findIndexField() {
		return $this->fieldsWithNames($this->indexFields);
	}
	function & field($s) {
		return $this-> $s;
	}
	function fieldID($s) {
		return $s;
	}

	function & visit(& $visitor) {
		return $this->accept($visitor);
	}

	function accept(&$visitor) {
		return $visitor->visitedPersistentObject($this);
	}

	function checkNotEmpty($fields, & $error_msgs) {
		$ret = true;
		$is_valid = false;
		foreach ($fields as $field) {
			$is_valid = $this-> $field->getValue() != "";
			if (!$is_valid) {
				$error_msgs[$field] = "Fill in the " . $this->$field->displayString . ", please";
			}
			$ret &= $is_valid;
		}
		return $ret;
	}
	/*@deprecated*/
	function check_not_null($fields, & $error_msgs) {
		return $this->checkNotEmpty($fields, $error_msgs);
	}
	function checkOneOf($fields, $error_msg, & $error_msgs) {
		$ret = false;
		foreach ($fields as $field) {
			if (!isset ($first_field))
				$first_field = $field;
			$ret |= $this-> $field->getValue() != "";
		}
		if (!$ret) {
			$error_msgs[$first_field] = $error_msg;
		}
		return $ret;
	}
	function validate(& $error_msgs) {
		return true;
	}
	/* Population */
	function populate($form, & $error_msgs) {
		$success = true;
		foreach ($this->allFieldNames() as $index) {
			// Populate the object "allFields()"
			$field = & $this-> $index;
			$success = $success && $this->populateField($field, $form, $error_msgs);
		}
		// Check object
		$success = $success && $this->obj->validate(& $error_msgs);
		//if (!$success) trace(print_r($error_msgs, TRUE));
		return $success;
	}
	function populateField(& $field, & $form, & $error_msgs) {
		// Checks the field data
		if (!$field->populate($form)) {
			$error_msgs[$field->colName] = "The " . $field->colName . " is invalid";
			return false;
		}
		return true;
	}
	function toArray() {
		$arr = array ();
		foreach ($this->allFields() as $index => $field) {
			$arr[$index] = $field->toArrayValue();
		}
		return $arr;
	}

	/* Helper methods for fields addition */

	function addTextField($name, $params = array ()) {
		$this->addField(new TextField($name, $params));
	}

	function addPasswordField($name, $params = array ()) {
		$this->addField(new PasswordField($name, $params));
	}

	function addTextArea($name, $params = array ()) {
		$this->addField(new TextArea($name, $params));
	}

	function addIndexField($name, $params = array ()) {
		$params['is_index'] = true;
		$this->addField(new IndexField($name, $params));
	}

	function addCollectionField($name, $params = array ()) {
		$this->addField(new CollectionField($name, $params));
	}

	function addNumField($name, $params = array ()) {
		$this->addField(new NumField($name, $params));
	}

	function addBoolField($name, $params = array ()) {
		$this->addField(new BoolField($name, $params));
	}

	function &copy() {
		$copy =& parent::copy();

		$copy->table = $this->table;

		// Not sure about copying children and parent
		if ($this->parent != null)
			$copy->parent =& $this->parent->copy();

		foreach($this->allFieldsThisLevel() as $field) {
			$copy->addField($field->copy());
		}

		$copy->displayString = $this->displayString;

		return $copy;
	}
}
?>