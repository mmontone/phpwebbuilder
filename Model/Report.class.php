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
	function setCondition($field, $comparator, $value){
		$this->conditions[$this->parseField($field)]=array($comparator,$value);
		$this->elements=null;
	}
	function parseField($f){
		return str_replace('.','`.`',$f);
	}
	function fieldNames(){
		foreach($this->fields as $f=>$n){
			if (!is_numeric($f)){
				$ret []= $f .' as `'. $n.'`';
			} else {
				$ret []= '`'. $this->parseField($n).'`';
			}
		}
		return implode(',',$ret);
	}
	function tableNames(){
		return implode(',',$this->tables);
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
		return 'SELECT ' . $this->fieldNames() . ' FROM ' . $this->restrictions() . $this->order() . $this->limit();
	}

	function refresh() {
		$this->elements = array();
	}
	function restrictions() {
		return $this->tableNames() . $this->conditions() . $this->group();
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
		return 'DescriptedObject';
	}
	function &makeElement($data){
		$dt = $this->getDataType();
		$o =& new $dt;
	 	foreach($data as $n=>$m){
	 		$o->$n =& new ValueHolder($m);
	 	}
	 	return $o;
	}

}
?>