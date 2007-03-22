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
	var $modified = false;
	/**
	 * Validation errors for the object
	 */
	var $validation_errors = array();
	/**
	 * Commits the changes to each field.
	 */
	var $toPersist = false;

	function commitChanges() {
		//print_backtrace('Committing changes');
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->commitChanges();
		}
		$this->setModified(false);
		$this->triggerEvent('changes_committed', $this);
	}

	function primitiveCommitChanges() {
		//print_backtrace('Primitive committing changes');
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->primitiveCommitChanges();
		}
		$this->setModified(false);
		$this->triggerEvent('changes_committed', $this);
	}

    function debugPrintString() {
    	$ret = array();

        $idFields = $this->allFields();
        foreach ($idFields as $index => $field) {
            $ret []= $index . ':' .  $field->viewValue();
        }
        $ret = implode(',',$ret);
        return $this->primPrintString($ret);
    }
	/**
	 * Removes all changes made to the object
	 */
	function flushChanges() {
		//print_backtrace('Flushing changes');
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->flushChanges();
		}
		$this->setModified(false);
	}
	/**
	 * Returns if the object was modified
	 */
	function isModified() {
		return $this->modified;
	}

	function setModified($b) {
		//print_backtrace(get_class($this) . '(' . $this->__instance_id . ') set modified: ' . $b);
		#@gencheck
        if ($this->isDeleted()) {
        	print_backtrace_and_exit('Error: modifying a deleted object ( ' . $this->printString() . ')');
        }//@#
        $this->modified = $b;
		if ($b) $this->registerModifications();
	}

    function setDeleted($b) {
    	$this->__deleted = true;
    }

    function isDeleted() {
    	return $this->__deleted;
    }

    function registerForPersistence() {
    	$this->toPersist = true;
    }

	function registerModifications(){
		if ($this->isPersisted()){
			$db =& DBSession::Instance();
			$db->registerObject($this);
		}
        #@sql_echo
        else {
            echo 'Not registering modification of ' . $this->printString() . '<br/>';
        }//@#
	}
	function registerPersistence(){
		if (!$this->isPersisted()){
			$db =& DBSession::Instance();
			$db->registerObject($this);
		}
	}
	function isPersisted(){
		return $this->existsObject || $this->toPersist;
	}
	function registerCollaborators(){
		foreach($this->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->registerCollaborators();
		}
	}
	/**
	 * Initializes the default fields for the object
	 */
	function basicInitialize() {
		$this->addField(new idField("id", FALSE));
		$this->addField(new VersionField("PWBversion", FALSE));
		$this->addGCFields();
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
		// Do not update if modified
		if ($this->isModified()) return true;

        // Do not update if incorrect version
        $version_field =& $this->PWBversion;
        if (!$version_field->shouldLoadFrom($reg)) {
        	return true;
        }

        // TODO LATER
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
			$this->primitiveCommitChanges();
			$this->existsObject = TRUE;
			return true;
		}
	}

    function attachFieldsEvents() {
        foreach($this->allFieldNames() as $f) {
            $field =& $this->fieldNamed($f);
            $field->addInterestIn('changed', new FunctionObject($this, 'fieldChanged'), array('execute on triggering' => true));
        }
    }
	/**
	 * Returns if the object is not a top class (that is, it's direct superclass is not persistent or descripted object)
	 */
	function isNotTopClass($class) {
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
		#@typecheck $field:ValueModel@#
		$name = $field->varName;
		#@check !isset($this-> $name)@#
		$this-> $name = & $field;
		$this->fieldNames[$name] = $name;
		if ($field->isIndex()) {
			$this->indexFields[$name] = $name;
		}
		$field->owner = & $this;
	}
	/**
	 * Registers that the field was changed
	 */
	function fieldChanged(& $field) {
		$this->setModified(true);
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
	function checkEregField($field, $ereg, $message) {
		if (is_exception($ex =& $this->$field->validate_ereg($ereg, $message))) {
			$this->addValidationError($ex);
			return $ex;
		} else {
			return false;
		}
	}
	function addValidationError(&$error) {
		$this->validation_errors[] =& $error;
	}

	/**deprecated*/
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
		$this->beValid();
		$this->validateFields();
		$this->validateObject();

		#@php5
        if (!$this->isValid()) {
        	$ex =& new PWBValidationError(array('object' => $this));
            $ex->raise();
        }//@#
        return $this->isValid();
	}
	/**
	 * validates the object, and return if it's valid
	 */
	function validateObject() {
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
    	return $this->fieldNamesPrefixed($operation, 'target_');
    }

    function fieldNamesPrefixed($operation, $prefix) {
		if ($operation=='SELECT'){
			if (!isset($this->allFieldsNamesAllLevels)){
				$fieldnames = array();
				$fs =& $this->allFieldsAllLevels();
		        foreach ($fs as $name => $field) {
				    $fn = $field->fieldNamePrefixed($operation, '{$prefix}');
		            if ($fn != null) {
		            	$fieldnames[] = $fn;
		            }
				}
		        $this->allFieldsNamesAllLevels=  implode(',', $fieldnames);
			}
			$fieldsNames = eval('return "'.$this->allFieldsNamesAllLevels.'";');
		} else {
				if (!isset($this->allFieldsNamesThisLevel)){
				$fieldnames = array();
				$fs =& $this->allFieldsThisLevel();
		        foreach ($fs as $name => $field) {
				    $fn = $field->fieldNamePrefixed($operation, '{$prefix}');
		            if ($fn != null) {
		            	$fieldnames[] = $fn;
		            }
				}
		        $this->allFieldsNamesThisLevel=  implode(',', $fieldnames);
			}
			$fieldsNames = eval('return "'.$this->allFieldsNamesThisLevel.'";');
		}
		return $fieldsNames;
    }
}

class PWBValidationError extends PWBException {

}


?>