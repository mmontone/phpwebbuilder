<?
class PersistentCollection extends Report{
	/**
	 * A collection of persisted objects (of the same class)
	 */
	function PersistentCollection($dataType = "") {
		$this->dataType = $dataType;
		parent::Collection();
	}
	/**
	 * finds all similar objects (objects with same atributes set in same values)
	 * Returns a PersistentCollection
	 */
	function &findMatches(&$object) {
		$col =& $object->findMatches();
		$this->conditions = $col->conditions;
		return $this;
	}
	/**
	  * Returns the tables to be used
	  */
	function tableNames() {
		$obj = new $this->dataType;
		return $obj->tableNames();
	}
	/**
	  * Returns the tables of the base class of the elements of the collection
	  */
	function tableName() {
		$obj = new $this->dataType;
		return $obj->tableName();
	}
	/**
	  * Returns the object of the collection with the id
	  */
	function & getObj($id) {
		return PersistentObject::getWithId($this->dataType, $id);
	}
	/**
	  * Creates an element, and fills it from the record
	  */
	function &makeElement($data){
		$dt = $this->getDataType();
		$obj =& new $dt;
		return $obj->loadFromRec($data);
	}
}

?>