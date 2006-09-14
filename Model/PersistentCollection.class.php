<?
class PersistentCollection extends Report{
	var $order;
	var $size;
	var $elements=array();

	function PersistentCollection($dataType = "") {
		$this->dataType = $dataType;

		parent::Collection();
	}

	function findMatches(&$object) {
		foreach($object->fieldNames() as $f) {
			$field =& $object->fieldNamed($f);
			if (!$field->isEmpty()) {
				$this->setCondition($f, '=', $field->getValue());
			}
		}
	}

	function findById($id) {
		return PersistentObject::getWithId($this->dataType, $id);
	}
	function tableNames() {
		$obj = new $this->dataType;
		return $obj->tableNames();
	}
	function tableName() {
		$obj = new $this->dataType;
		return $obj->tableName();
	}
	function idRestrictions(){
		$obj = new $this->dataType;
		return $obj->idRestrictions();
	}
	function allFields(){
		$obj = new $this->dataType;
		return $obj->allIndexFields();
	}
	function fieldNames(){
		$obj = & new $this->dataType;
		return $obj->fieldNames('SELECT');
	}

	function add(&$element) {
		$elements =& $this->elements();
		$elements[] =& $element;
		$this->triggerEvent('changed', $n=null);
	}

	function & getObj($id) {
		return PersistentObject::getWithId($this->dataType, $id);
	}

	function getSize($conditions='1=1') {
		$obj = & new $this->dataType;
		$sql = 'SELECT COUNT(id) as \'collection_size\' FROM ' . $this->tableName() . ' WHERE ' . $conditions;
		$db = & DB::Instance();
		$reg = $db->SQLExec($sql, FALSE, $this);
		$data = $db->fetchrecord($reg);
		return $data['collection_size'];
	}
	function &makeElement($data){
		$dt = $this->getDataType();
		$obj =& new $dt;
		return $obj->loadFromRec($data);
	}
}

?>