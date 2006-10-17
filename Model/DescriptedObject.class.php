<?php

class DescriptedObject extends PWBObject {
	/**
	 * Parent of the object (inheritance-wise)
	 */
	var $parent = NULL;
	/**
	 * Names of the fields of the object
	 */
    var $fieldNames = array ();
	/**
	 * Names of the index fields of the object
	 */
	var $indexFields = array ();
	/**
	 * Nice looking name for the object's class
	 */
	var $displayString;
	/**
	 * If the object was modified. Defaults to true
	 * (the object was modified, as it was created)
	 */
	var $modified = true;
	/**
	 * Validation errors for the object
	 */
	var $validation_errors = array();
	/**
	 * Commits the changes to each field.
	 */
	function commitChanges() {
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->commitChanges();
		}
		$this->modified = false;
		$this->triggerEvent('changes_committed', $this);
	}
	/**
	 * Prints a visual representation of the object (the values of
	 * it's index fields)
	 */
	function printString(){
		return $this->indexValues();
	}
	/**
	 * Removes all changes made to the object
	 */
	function flushChanges() {
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->flushChanges();
		}
		$this->modified = false;
	}
	/**
	 * Returns if the object was modified
	 */
	function isModified() {
		return $this->modified;
	}
	/**
	 * Initializes the default fields for the object
	 */
	function basicInitialize() {
		$this->addField(new idField("id", FALSE));
		$this->addField(new VersionField("PWBversion", FALSE));
		$this->PWBversion->setValue(0);
		if ($this->isNotTopClass($this)) {
			$this->addField(new superField("super", FALSE));
		}
		$this->displayString = ucfirst(getClass($this));
		if($this->table==''){
			$this->table = getClass($this);
		}

		$this->initialize();
	}
	/**
	 * Initializes the fields for the object
	 */
	function initialize(){}

	/**
	 * Loads the object's values from an associative array
	 */
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
			$this->setID($this->id->getValue());
			$this->commitChanges();
			$this->existsObject = TRUE;
			return true;
		}
	}
	/**
	 * Returns if the object is not a top class (that is, it's direct superclass is not persistent or descripted object)
	 */
	function isNotTopClass(& $class) {
		$cn = strtolower(get_parent_class($class));
		return (strcmp($cn, 'persistentobject') != 0 && strcmp($cn, 'descriptedobject') != 0);
	}
	/**
	 * Returns this level's field (inheritance-wise)
	 */
    function & allFieldsThisLevel() {
		return $this->fieldsWithNames($this->allFieldNamesThisLevel());
	}
	/**
	 * Returns all the fields
	 */
	function & allFields() {
		return $this->fieldsWithNames($this->allFieldNames());
	}
	/**
	 * Returns the fields with specified names
	 */
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
	/**
	 * Returns all the field names
	 */
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
	/**
	 * Returns all the index field's names
	 */
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
	/**
	 * Returns all the index field's
	 */
	function & allIndexFields() {
		return $this->fieldsWithNames($this->allIndexFieldNames());
	}
	/**
	 * Returns this level's field names (inheritance-wise)
	 */
	function allFieldNamesThisLevel() {
		return $this->fieldNames;
	}
	/**
	 * sets the field with the name
	 */
	function setField($name, & $field) {
		$this-> $name = & $field;
	}
	/**
	 * Returns the field with specified name
	 */
	function & fieldNamed($name) {
		$f = & $this-> $name;
		return $f;
	}
	/**
	 * Returns the fields with specified names
	 */
	function & getFields($fs) {
		$arr = array ();
		foreach ($fs as $f) {
			$arr[] = & $this->fieldNamed($f);
		}
		return $arr;
	}
	/**
	 * Adds a field to the object
	 */
	function addField(& $field) {
		$name = $field->colName;
		$this-> $name = & $field;
		$this->fieldNames[$name] = $name;
		if ($field->isIndex()) {
			$this->indexFields[$name] = $name;
		}
		$field->owner = & $this;

		$field->addInterestIn('changed', new FunctionObject($this, 'fieldChanged'));
	}
	/**
	 * Registers that the field was changed
	 */
	function fieldChanged(& $field) {
		$this->modified = true;
		$this->triggerEvent('changed', $this);
	}
	/**
	 * Returns the id of the field
	 */
	function fieldID($s) {
		return $s;
	}
	/**
	 * Visitor
	 */
	function & visit(& $visitor) {
		return $this->accept($visitor);
	}
	/**
	 * Visitor
	 */
	function accept(&$visitor) {
		return $visitor->visitedPersistentObject($this);
	}
	/**
	 * Checks if the fields with the specified names are not empty
	 */
	function checkNotEmpty($fields) {
		$ret = false;
		$ex = array();
		$i = 1;

		foreach ($fields as $field) {
			$ret = $ret or $this->checkNotEmptyField($field, 'Fill in the ' . $this->$field->displayString . ', please');
		}
		return $ret;
	}
	/**
	 * Checks if the field with the specified name is not
	 * empty, and returns a validationException with the message
	 * otherwise
	 */
	function checkNotEmptyField($field, $message) {
		if ($this->$field->isEmpty()) {
			$this->$field->requiredButEmpty();
			$error =& new EmptyFieldException(array('message' => $message, 'content' => & $this->$field));
			$this->addValidationError($error);
			return $error;
		}
		else {
			return false;
		}
	}

	function addValidationError(&$error) {
		$this->validation_errors[] =& $error;
	}

	/*@deprecated*/
	function check_not_null($fields, & $error_msgs) {
		return $this->checkNotEmpty($fields);
	}
	/**
	 * Checks if at least one of the fields with these names is not empty
	 */
	function checkOneOf($field_names, $error_msg) {
		$ret = false;

		foreach ($field_names as $field) {
			$ret |= ! $this-> $field->isEmpty();
		}

		if (!$ret) {
			$fields =& $this->getFieldsNamed($field_names);
			$ex =& new OneOfException(array('message' => $error_msg, 'content' => $fields));
			$this->validation_errors[] =& $ex;
			return $ex;
		}

		return false;
	}
	/**
	 * Validates each fields, and validates the object's specific restrictions
	 */
	function validateAll() {
		$this->validateObject();
		$this->validateFields();

		return !empty($this->validation_errors);
	}
	/**
	 * validates the object, and return if it's valid
	 */
	function validateObject() {
		$this->beValid();
		$this->validate();
		return $this->isValid();
	}
	/**
	 * Checks if there are any validation errors
	 */
	function isValid() {
		return empty($this->validation_errors);
	}
	/**
	 * Removes all validation errors
	 */
	function beValid() {
		$n = array();
		$this->validation_errors =& $n;

		foreach ($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->validated();
		}
	}
	/**
	 * Validates each field
	 */
	function validateFields() {
		$ret = true;

		foreach ($this->allFieldNames() as $f) {
			if (!$this->validateField($f)) {
				$ret = false;
			}
		}

		return $ret;
	}
	/**
	 * Validates the field. Returns if it's valid
	 */
	function validateField($field_name) {
		$field =& $this->fieldNamed($field_name);
		if ($ex =& $field->validate()) {
			$this->validation_errors[] =& $ex;
			return false;
		}

		return true;
	}
	/**
	 * Returns an exception, or false if there was no error
	 */
	function validate() {
		return false;
	}
    /**
	 * Returns a SQL string for accesing the fields for the specified operation
	 */

	function fieldNames($operation) {
		$fieldnames = '';
		if ($operation=='SELECT'){
			$fs =& $this->allFieldsAllLevels();
		} else {
			$fs =& $this->allFieldsThisLevel();
		}
		foreach ($fs as $name => $field) {
			$fieldnames .= $field->fieldName($operation);
		}
		$fieldnames = substr($fieldnames, 0, -2);
		return $fieldnames;
	}

}
?>