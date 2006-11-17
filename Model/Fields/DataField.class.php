<?

class DataField extends ValueModel {
	/**
	 * The field name
	 */
	var $colName;
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
		$this->colName = $ps['fieldName'];
		$this->isIndex = $ps['is_index'];
		$this->displayString = $ps['display'];
	}
	/**
	 * Returns the default initialization values of the object
	 */
	function defaultValues($params){
		return array(
				'is_index'=>false,
				'display'=>ucfirst($params['fieldName'])
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
		if ($operation=='SELECT'){
			return $this->owner->tableName().'.`'.$this->colName
				   .'` as `'.$this->sqlName().	'`, ';
		} else {
			return '`'.$this->colName .	'`, ';
		}
	}
	/**
	 * Returns the sql name of the field
	 */
	function sqlName(){
		return $this->owner->getTable().'_'.$this->colName;
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
		if ($data !== $this->buffered_value) {
			$this->buffered_value =& $data;
			$this->setModified(true);
			$this->triggerEvent('changed', $no_params = null);
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
			$this->triggerEvent('changed', $no_params = null);
		}
	}

	function primitiveCommitChanges() {
		$this->value =& $this->buffered_value;
	}
	function primitiveFlushChanges() {
		$this->setValue($this->value);
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
		$val = $reg[$this->sqlName()];
		$this->setValue($val);
	}
	/**
	 * Validates, and returns false
	 */
	function validate() {
		$this->validated();
		return false;
	}
	/**
	 * Triggers a validated event
	 */
	function validated() {
		$this->triggerEvent('validated', $this);
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
}

?>