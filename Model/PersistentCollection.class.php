<?
$DBObject = DBObject;

class PersistentCollection extends Collection{
	var $order;
	var $conditions;
	var $dataType;
	var $limit = 0;
	var $offset = 0;
	var $size;
	var $elements=array();

	function PersistentCollection($dataType = "") {
		$this->dataType = $dataType;
		$this->conditions = array ();
		$this->order=array();

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
	function setCondition($field, $comparator, $value){
		$this->conditions[$field]=array($comparator,$value);
		$this->elements=null;
	}
	function conditions() {
		$cond = '1=1';//$this->idRestrictions();
		foreach ($this->conditions as $f => $c) {
			$cond .= ' AND `'. $f .'` '. $c[0] .' '. $c[1];
		}
		$cond = ' WHERE ' . $cond;
		return $cond;
	}

	function orderByFields($fields) {
		foreach($fields as $field=>$order) {
			$this->orderBy($field, $order);
		}
	}

	function orderBy($fieldname, $order='ASC') {
		$this->order[$fieldname] = $order;
	}

	function unordered() {
		$order = array();
		$this->order =& $order;
	}

	function order() {
		if (empty($this->order)) return '';

		$orders = array();
		foreach ($this->order as $f => $c) {
			$orders[] = '`'. $f .'` '. $c;
		}
		return ' ORDER BY ' . implode(',', $orders);
	}

	function visit($obj) {
		return $obj->visitedPersistentCollection($this);
	}
	function limit() {
		if ($this->limit != 0)
			return ' LIMIT ' . $this->limit . $this->offset();
	}
	function offset() {
		return ' OFFSET ' . $this->offset;
	}
	function restrictions() {
		return $this->tableNames() . $this->conditions();
	}
	function selectsql(){
		$obj = & new $this->dataType;
		return 'SELECT ' . $obj->fieldNames('SELECT') . ' FROM ' . $this->restrictions() . $this->order() . $this->limit();
	}

	function refresh() {
		$this->elements = array();
	}
	function &elements() {
		if (empty($this->elements)){
			$obj = & new $this->dataType;
			$sql = $this->selectsql();
			$db =& DB::Instance();
			$reg = $db->SQLExec($sql, FALSE, $this);
			if ($reg===false) return false;
			$col = array ();
			while ($data = $db->fetchrecord($reg)) {
				$col[] =& $obj->loadFromRec($data);
			}
			$this->elements =& $col;
		}
		return $this->elements;
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
}

?>