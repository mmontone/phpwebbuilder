<?php

class Report extends Collection{
	/** Used with Collection Navigator.
	 * Should extend (or return) Collection.
	 * Elements should be descripted objects.
	 *
	 *
	 */
	 var $tables=array();
	 var $fields = array();
	 var $conditions = array ();
	 var $order=array();
	 var $dataType = 'DescriptedObject';
	function setCondition($field, $comparator, $value){
		$this->conditions[$this->parseField($field)]=array($comparator,$value);
		$this->elements=null;
	}
	function removeCondition($field){
		unset($this->conditions[$this->parseField($field)]);
		$this->elements=null;
	}
	function size() {
		$sql = 'SELECT COUNT(';
		$g = $this->group();
		if ($g!=''){
			$sql .= 'DISTINCT '.substr($g, 10);
		}else{
			$sql .= '*';
		}
		$sql .=') as collection_size FROM ' . $this->restrictions();
		$db = & DB::Instance();
		$reg = $db->query($sql);
		if ($reg===false) {
			return false;
		} else {
			$data = $db->fetchrecord($reg);
			return $data['collection_size'];
		}
	}
	function allFields(){
		$obj =& $this->getObject();
		$arr = array_merge($obj->allIndexFieldNames(), $this->fields);
		return $arr;
	}
	function parseField($f){
		return str_replace('.','`.`',$f);
	}
	function &getObject(){
		$dt = $this->getDataType();
		return new $dt;
	}
	function fieldNames(){
		foreach($this->fields as $f=>$n){
			if (!is_numeric($f)){
				$ret []= $f .' as `'. $n.'`';
			} else {
				$ret []= '`'. $this->parseField($n).'`';
			}
		}
		$obj =& $this->getObject();
		$ret []= $obj->fieldNames('SELECT');
		return  implode(',',$ret);
	}
	function tableNames(){
		return implode(',',$this->tables);
	}
	function discardConditions(){
		$this->conditions = array();
	}
	function conditions() {
		$cond = '1=1';
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
		$this->order[$this->parseField($fieldname)] = $order;
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
	function groupBy($fieldname) {
		$this->group[] = $this->parseField($fieldname);
	}

	function ungrouped() {
		$order = array();
		$this->group =& $order;
	}

	function group() {
		if (empty($this->group)) return '';

		$orders = array();
		foreach ($this->group as $f) {
			$orders[] = '`'. $f .'` ';
		}
		return ' GROUP BY ' . implode(',', $orders);
	}
	function limit() {
		if ($this->limit != 0)
			return ' LIMIT ' . $this->limit . $this->offset();
	}
	function offset() {
		return ' OFFSET ' . $this->offset;
	}
	function selectsql(){
		return 'SELECT ' . $this->fieldNames() . ' FROM ' . $this->restrictions()  .$this->group(). $this->order() . $this->limit();
	}

	function refresh() {
		$this->elements = array();
	}
	function restrictions() {
		return $this->tableNames() . $this->conditions();
	}
	function &elements() {
		if (empty($this->elements)){
			$sql = $this->selectsql();
			$db =& DB::Instance();
			$reg = $db->SQLExec($sql, FALSE, $this);
			if ($reg===false) return false;
			while ($data = $db->fetchrecord($reg)) {
				$this->elements[] =& $this->makeElement($data);
			}
		}
		return $this->elements;
	}
	function getDataType(){
		return $this->dataType;
	}
	function &makeElement($data){
		$obj =& $this->getObject();
		$ret []= $obj->fieldNames('SELECT');
		$o =& $obj->loadFromRec($data);
	 	foreach($data as $n=>$m){
	 		if (!isset($ret[$n])){
	 			$o->$n =& new ValueHolder($m);
	 		}
	 	}
	 	return $o;
	}

}
?>