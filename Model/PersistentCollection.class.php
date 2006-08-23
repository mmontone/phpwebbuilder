<?
class PersistentCollection extends Report{
	var $order;
	var $dataType;
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

	/* Deprecated */
	function &objects() {
		return $this->elements();
	}

	function size() {
		$obj = & new $this->dataType;
		$sql = 'SELECT COUNT(*) as collection_size FROM ' . $this->restrictions();
		$db = & DB::Instance();
		$reg = $db->query($sql);
		if ($reg===false) {
			return false;
		} else {
			$data = $db->fetchrecord($reg);
			return $data['collection_size'];
		}
	}

	function & getObj($id) {
		return PersistentObject::getWithId($this->dataType, $id);
	}

	function toArray() {
		$objs = $this->objects();
		$arr = array ();
		foreach ($objs as $obj) {
			$arr[] = $obj->toArray();
		}
		return $arr;
	}

	function toPlainArray() {
		$arr = array (
			"offset" => $this->offset,
			"limit" => $this->limit,
			"dataType" => $this->dataType,
			"order" => $this->order,
			"conditions" => ereg_replace("\"",
			"%22",
			ereg_replace("\'",
			"%27",
			ereg_replace("%",
			"%25",
			serialize($this->conditions
		)))), "ObjType" => getClass($this));
		return $arr;
	}

	/*------------- new ----------------*/

/*
	function fetchElements($offset, $limit, $order, $conditions='1=1') {
		$obj = & new $this->dataType;
		$sql = 'SELECT ' . $obj->fieldNames('SELECT') . ' FROM ' . $this->tableNames() . ' WHERE ' . $conditions .
				$order . ' LIMIT ' . $limit . $offset;
		$db =& DB::Instance();
		$reg = $db->SQLExec($sql, FALSE, $this);
		$col = array ();
		while ($data = $db->fetchrecord($reg)) {
			$obj = & PersistentObject :: getWithId($this->dataType, $data['id']);
			$col[] = & $obj;
		}
		return $col;
	}*/

	function getSize($conditions='1=1') {
		$obj = & new $this->dataType;
		$sql = 'SELECT COUNT(id) as \'collection_size\' FROM ' . $this->tableName() . ' WHERE ' . $conditions;
		$db = & DB::Instance();
		$reg = $db->SQLExec($sql, FALSE, $this);
		$data = $db->fetchrecord($reg);
		return $data['collection_size'];
	}
	function getDataType(){
		return $this->dataType;
	}

	function &makeElement($data){
		$dt = $this->getDataType();
		$obj =& new $dt;
		return $obj->loadFromRec($data);
	}
}

?>