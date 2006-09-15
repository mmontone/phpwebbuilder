<?php

class Report extends Collection{
	/**
	 * Used with Collection Navigator.
	 * Elements should be descripted objects.
	 */
	/**
	 * Tables involved
	 */
	 var $tables=array();
	/**
	 *  The extra fields to return
	 */
	 var $fields = array();
	 /**
	 * The conditions which the elements must satisfy
	 */
	 var $conditions = array ();
	 /**
	 * The order in which the elements must be returned
	 */
	 var $order=array();
	 /**
	 * The class of the elements to be returned
	 */
	 var $dataType = 'DescriptedObject';
	 /**
	  * Adds a condition to filter the data
	  */
	function setCondition($field, $comparator, $value){
		$this->conditions[$this->parseField($field)]=array($comparator,$value);
		$this->elements=null;
	}
	/**
	  * removes a condition
	  */
	function removeCondition($field){
		unset($this->conditions[$this->parseField($field)]);
		$this->elements=null;
	}
	/**
	  * Removes all conditions
	  */
	function discardConditions(){
		$this->conditions = array();
	}
	/**
	  * Creates a WHERE for the SQL query, based on the report's conditions
	  */

	function conditions() {
		$cond = '1=1';
		foreach ($this->conditions as $f => $c) {
			$cond .= ' AND `'. $f .'` '. $c[0] .' '. $c[1];
		}
		$cond = ' WHERE ' . $cond;
		return $cond;
	}
	/**
	  * Returns the size of the collection
	  */
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
	/**
	 * Returns the field of the datatype, plus the extra fields requested
	 */
	function allFields(){
		$obj =& $this->getObject();
		$arr = array_merge($obj->allIndexFieldNames(), $this->fields);
		return $arr;
	}
	/**
	  * Encapses the field name in backquotes
	  */
	function parseField($f){
		return str_replace('.','`.`',$f);
	}
	/**
	  * Returns a new object of the dataype
	  */
	function &getObject(){
		$dt = $this->getDataType();
		return new $dt;
	}
	/**
	  * Returns all the field names, backquote encapsed
	  */
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
	/**
	  * Returns the tables to be used
	  */
	function tableNames(){
		return implode(',',$this->tables);
	}
	/**
	  * Sets an order based on the field=>order array parameter
	  */

	function orderByFields($fields) {
		foreach($fields as $field=>$order) {
			$this->orderBy($field, $order);
		}
	}
	/**
	  * Adds the order for the field, in ASC or DESC order
	  */

	function orderBy($fieldname, $order='ASC') {
		$this->order[$this->parseField($fieldname)] = $order;
	}
	/**
	  * Removes all ordering
	  */

	function unordered() {
		$order = array();
		$this->order =& $order;
	}
	/**
	  * Creates a ORDER for the SQL query, based on the report's orderings
	  */

	function order() {
		if (empty($this->order)) return '';

		$orders = array();
		foreach ($this->order as $f => $c) {
			$orders[] = '`'. $f .'` '. $c;
		}
		return ' ORDER BY ' . implode(',', $orders);
	}
	/**
	  * Adds a grouiping field
	  */

	function groupBy($fieldname) {
		$this->group[] = $this->parseField($fieldname);
	}
	/**
	  * Removes all field grouping
	  */

	function ungrouped() {
		$order = array();
		$this->group =& $order;
	}
	/**
	  * Creates a GROUP BY for the SQL query, based on the report's groupings
	  */

	function group() {
		if (empty($this->group)) return '';

		$orders = array();
		foreach ($this->group as $f) {
			$orders[] = '`'. $f .'` ';
		}
		return ' GROUP BY ' . implode(',', $orders);
	}
	/**
	  * Returns the limit and offset SQL, in case they are set
	  */

	function limit() {
		if ($this->limit != 0)
			return ' LIMIT ' . $this->limit . $this->offset();
	}
	/**
	  * Returns the offset SQL
	  */

	function offset() {
		return ' OFFSET ' . $this->offset;
	}
	/**
	  * Returns the complete query for filling the report
	  */

	function selectsql(){
		return 'SELECT ' . $this->fieldNames() . ' FROM ' . $this->restrictions()  .$this->group(). $this->order() . $this->limit();
	}
	/**
	  * Removes all cached elements
	  */

	function refresh() {
		$this->elements = array();
	}
	/**
	  * Returns the restrictions (which rows, and which tables)
	  */

	function restrictions() {
		return $this->tableNames() . $this->conditions();
	}
	/**
	  * Returns an array of all the elements (considering limit and offset)
	  */

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
	/**
	  * Returns the datatype
	  */

	function getDataType(){
		return $this->dataType;
	}
	/**
	  * Creates an element, and fills it from the record
	  */

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