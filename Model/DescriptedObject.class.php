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
    var $indexFieldsLevel = array ();
    var $fields = array ();
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
		foreach($this->metadata->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->commitChanges();
		}
		$this->setModified(false);
		$this->triggerEvent('changes_committed', $this);
	}

	function primitiveCommitChanges() {
		//print_backtrace('Primitive committing changes');
		foreach($this->metadata->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->primitiveCommitChanges();
		}
		$this->setModified(false);
		$this->triggerEvent('changes_committed', $this);
	}

    function debugPrintString() {
    	$ret = array();

		$md =& PersistentObjectMetaData::getMetaData(getClass($this));
        $idFields = $md->allIndexFieldNames();
        foreach ($idFields as $field) {
            $ret []= $field . ':' .  $this->$field->viewValue();
        }
        $ret = implode(',',$ret);
        return $this->primPrintString($ret);
    }
	/**
	 * Removes all changes made to the object
	 */
	function flushChanges() {
		//print_backtrace('Flushing changes');
		foreach($this->metadata->allFieldNames() as $f) {
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
		$this->modified = $b;
		if ($b) $this->registerModifications();
	}

    function registerForPersistence() {
    	$this->toPersist = true;
    }

	function registerModifications(){
		if ($this->isPersisted()){
			$db =& DBSession::Instance();
			$db->registerObject($this);
		}
        #@persistence_echo
        else {
            echo 'Not registering modification of ' . $this->debugPrintString() . '<br/>';
        }//@#
	}
	function registerPersistence(){
		if (!$this->isPersisted()){
			$db =& DBSession::Instance();
			$db->registerObject($this);
			#@persistence_echo
            echo 'Registering persistence of ' . $this->debugPrintString() . '<br/>';
        	//@#
		}
			#@persistence_echo
			else {
				echo 'NOT registering persistence of ' . $this->debugPrintString() . '<br/>';
			}
			//@#
	}
	function isPersisted(){
		return $this->existsObject or $this->toPersist;
	}
	function registerCollaborators(){
		foreach($this->metadata->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->registerCollaborators();
		}
	}
	function getPersistentClasses(){
		$arr = array_reverse(get_superclasses_upto(getClass($this),'PersistentObject'));
		$arr[]=getClass($this);
		return $arr;
	}
	/**
	 * Initializes the default fields for the object
	 */
	function basicInitialize() {
		foreach($this->getPersistentClasses() as $sc){
			$this->table = $sc;
			$this->actClass = $sc;
			eval($sc.'::initialize();');
			$this->displayString = ucfirst(getClass($this));
			$this->addMetaField(new IdField("id", FALSE));
			$this->addMetaField(new VersionField("PWBversion", FALSE));
			if (DescriptedObject::isNotTopClass($sc)) {
				$this->addMetaField(new SuperField("super", FALSE));
			} else {
				$this->addGCFields();
			}
			$this->tables[$sc]=$this->table;
		}
		unset($this->actClass);
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
		$ok = true;
        foreach($this->getPersistentClasses() as $sc){
			$this->fields[$sc]['id']->loadFrom($reg);
			foreach (array_keys($this->fields[$sc]) as $index) {
				$field =&$this->fields[$sc][$index];
				$ok = $ok and $field->loadFrom($reg);
				$field->setID($this->fields[$sc]['id']->getValue());
			}
        }
        //if (DescriptedObject::isNotTopClass(getClass($this))) {header('Content-type: text/plain');unset($this->metadata);print_r($this->fields);exit;}
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
        foreach($this->metadata->allFieldNames() as $f) {
            $field =& $this->fieldNamed($f);
            $field->addInterestIn('changed', new FunctionObject($this, 'fieldChanged'), array('execute on triggering' => true));
        }
    }
	/**
	 * Returns if the object is not a top class (that is, it's direct superclass is not persistent or descripted object)
	 */
	function isNotTopClass($class) {
		#@gencheck if (is_object($class)) print_backtrace('not string');@#
		return is_strict_subclass(get_parent_class($class), 'persistentobject');
	}
	/**
	 * sets the field with the name
	 */
	function setField($name, & $field) {
		$this->$name = & $field;
	}
	/**
	 * Returns the field with specified name
	 */
	function & fieldNamed($name) {
		$f = & $this->$name;
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
		#@gencheck if (isset($this->{$field->varName})) print_backtrace($this->printString() . ' already has '.$name.' which is a '.getTypeOf($this->$name));@#
		$this->addMetaField($field);
	}
	function addMetaField(&$field){
		#@typecheck $field:ValueModel@#
		$name = $field->varName;
		$field->table = $this->table;
		$this->$name = & $field;
		$this->fieldNames[$name] = $name;
		$this->fields[$this->actClass][$name]=&$field;
		if ($field->isIndex()) {
			$this->indexFields[$name] = $name;
			$this->indexFieldsLevel[$this->actClass][$name]=$name;
		}
        $field->owner = & $this;
        $field->initialize();
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
	 * Returns all the fields
	 */
	function & allFields() {
		return $this->fieldsWithNames($this->metadata->allFieldNames());
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
			$ret |= ! $this->$field->isEmpty();
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
        	$ex =& new PWBValidationError(array('content' => array('object' => $this, 'errors' => $this->validation_errors)));
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

		foreach ($this->metadata->allFieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			$field->validated();
		}
	}
	/**
	 * Validates each field
	 */
	function validateFields() {
		$ret = true;

		foreach ($this->metadata->allFieldNames() as $f) {
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
}

class PWBValidationError extends PWBException {

}


?>