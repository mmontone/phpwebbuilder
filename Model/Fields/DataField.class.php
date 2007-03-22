<?

class DataField extends ValueModel {
	/**
	 * The column name
	 */
	var $colName;
	/**
	 * The field name
	 */
	var $varName;
	/**
	 * The Value
	 */
	var $value;
	/**
	 * If the field is an index field
	 */
	var $isIndex;
	/**
	 * The object the field belongs to
	 */
	var $owner;
	/**
	 * A nice name for the string
	 */
	var $displayString;
	/**
	 * The buffered value, if the data was modified
	 */
	var $buffered_value = null;
	/**
	 * if the data was modified
	 */
	var $modified = false;

	function DataField($name, $isIndex=null){
		if (!is_array($name)){
			$ps = array('fieldName'=>$name);
		} else {
			$ps = $name;
		}
		if (is_array($isIndex)){
			$ps = $isIndex;
			$ps['fieldName'] =$name;
		} else if ($isIndex!==null){
			$ps['is_index']= $isIndex;
		}
		parent::PWBObject($ps);
	}
	/**
	 * Returns if the field is an index field
	 */
	function isIndex() {
		return $this->isIndex;
	}
	/**
	 * Prepares the object to be saved (for cascading and inheritance-by-relation)
	 */
	function prepareToSave(){}
	/**
	 * Performs the needed actions to create a consistent instance
	 */
	function createInstance($ps) {
		$ps= $this->creationParams = array_merge($this->defaultValues($ps),$ps);
		$this->colName = $ps['columnName'];
		$this->varName = $ps['fieldName'];
		$this->isIndex = $ps['is_index'];
		$this->displayString = $ps['display'];
		$this->value = $ps['default'];
	}
	/**
	 * Returns the default initialization values of the object
	 */
	function defaultValues($params){
		return array(
				'is_index'=>false,
				'default'=>null,
				'display'=>ucfirst($params['fieldName']),
				'columnName'=>$params['fieldName']
			);
	}
	function & visit(& $obj) {
		return $obj->visitedDataField($this);
	}
	/**
	 * Receives the notification of id of the owner
	 */
	function setID($id) {}
	/**
	 * Returns name of the field for the specified operation
	 */
	function fieldName($operation) {
		return $this->fieldNamePrefixed($operation,'');
	}
	function registerCollaborators(){}
    function fieldNamePrefixed($operation, $prefix) {
        if ($operation=='SELECT'){
            return $this->owner->metadata->tableNamePrefixed($prefix).'.`'.$this->colName
                   .'` AS `'.$this->sqlName() .'`';
        } else {
            return '`'.$this->colName . '`';
        }
    }

	/**
	 * Returns the sql name of the field
	 */
	function sqlName(){
		return $this->owner->metadata->getTablePrefixed('').'_'.$this->varName;
	}
	/**
	 * Returns the sql value of the field
	 */
	function SQLvalue() {}
	/**
	 * Returns the sql insertion value of the field
	 */
	function insertValue() {
		return $this->SQLvalue();
	}
	/**
	 * Returns the sql update string
	 */
	function updateString() {
		return '`'.$this->colName . '` = ' . $this->SQLvalue();
	}
	/**
	 * Returns the value of the field
	 */
	function viewValue() {
		return $this->getValue();
	}
	/**
	 * Sets (buffers) the value of the field
	 */
	function setValue($data) {
		if ($data != $this->buffered_value) {
			$this->buffered_value = $data;
            $n = null;
            $this->setModified(true);
			$this->triggerEvent('changed', $this);
		}
	}
	/**
	 * Returns the value of the field
	 */
	function getValue() {
		if ($this->buffered_value !== null)
			return $this->buffered_value;
		else
			return $this->value;
	}
	/**
	 * Commits the changes on the field
	 */
	function commitChanges() {
		if ($this->isModified()) {
		    $this->primitiveCommitChanges();
			$this->setModified(false);
			$this->triggerEvent('commited', $this);
		}
	}
	/**
	 * Reverts the changes
	 */
	function flushChanges() {
		if ($this->isModified()) {
			$this->primitiveFlushChanges();
			$this->setModified(false);
			$this->triggerEvent('flushed', $this);
			//$this->triggerEvent('changed', $no_params = null);
		}
	}

	function primitiveCommitChanges() {
		$this->value = $this->buffered_value;
		$this->setModified(false);
	}
	function primitiveFlushChanges() {
		$this->setValue($this->value);
		$this->setModified(false);
	}
	/**
	 * Returns if the field was modified
	 */
	function isModified() {
		return $this->modified;
	}

	function setModified($b) {
		$this->modified = $b;
	}
	/**
	 * Loads the value from the record
	 */
	function loadFrom($reg) {
		$val = @$reg[$this->sqlName()];
		$this->setValue($val);
	}
	/**
	 * Validates, and returns false
	 */
	function &validate() {
		$this->validated();
		$f = false;
		return $f;
	}
	/**
	 * Triggers a validated event
	 */
	function validated() {
		$this->triggerEvent('validated', $this);
	}
	function &validate_ereg($regex, $mess){
			if (!ereg($regex, $this->getValue())) {
			$ex =& new ValidationException(array (
				'message' => $mess,
				'content' => & $this
			));
			$this->triggerEvent('invalid', $ex);
			return $ex;
		}

		$this->triggerEvent('validated', $this);
		$f = false;
		return $f;
	}
	/**
	 * Triggers a required_but_empty event
	 */
	function requiredButEmpty() {
		$this->triggerEvent('required_but_empty', $this);
	}
	/**
	 * Returns if the field can be deleted
	 */
	function canDelete() {
		return true;
	}
	/**
	 * Checks if the field is empty
	 */
	function isEmpty() {
		return $this->getValue() == '';
	}
	function printString(){
		return $this->primPrintString($this->colName);
	}

	//GARBAGE COLLECTION
	function mapChild($method){}
	function removedAsTarget(&$elem, $field){}
	function addedAsTarget(&$elem, $field){}
}

?>