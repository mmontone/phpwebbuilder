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

	  var $group = array();
	  var $vars = array();

	function addTable($table) {
		$this->tables[] = $table;
	}

	function setTables($tables) {
		$this->tables = $tables;
	}

	function addTables($tables) {
		foreach ($tables as $table) {
			$this->addTable($table);
		}
	}

	function setCondition($field, $comparator, $value){
		//echo 'Report: Setting condition: ' . $field . $comparator . $value . '<br />';
		$this->conditions[]=array($this->parseField($field),$comparator,$value);
		$n = null;
		$this->elements=& $n;
	}

	function setConditions($conditions) {
		foreach ($conditions as $condition) {
			$this->setCondition($condition[0], $condition[1], $condition[2]);
		}
	}

	function defineVar($id, $class) {
		$o =& new $class;
		$this->addTables($o->getTables());
		$this->vars[$id] =& $class;
	}

	function setPathCondition(&$condition) {
		$condition->applyTo($this);
	}
	/**
	  * removes a condition
	  */
	function removeCondition($field){
		unset($this->conditions[$this->parseField($field)]);
		$n = null;
		$this->elements=& $n;
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
		foreach ($this->getConditions() as $c) {
			$cond .= ' AND `'. $c[0] .'` '. $c[1] .' '. $c[2];
		}
		$cond = ' WHERE ' . $cond;
		return $cond;
	}

	function &getConditions() {
		return $this->conditions;
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
		$arr = array_merge($obj->allIndexFieldNames(), $this->getFields());
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
		$o  =& new $dt;
		return $o;
	}
	/**
	  * Returns all the field names, backquote encapsed
	  */
	function fieldNames(){
		foreach($this->getFields() as $f=>$n){
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

	function &getFields() {
		return $this->fields;
	}

	/**
	  * Returns the tables to be used
	  */
	function tableNames(){
		/*
		$tnames = array();
		foreach($this->getTables() as $table) {
			if (strstr($table, ' ')){
				$tnames[] = $table;
			} else {
				$tnames[] = '`' . $table . '`';
			}
		}
		return implode(',',$tnames);
		*/
		return implode(',', $this->getTables());
	}

	function &getTables() {
		return $this->tables;
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
		$order =& $this->getOrder();
		if (empty($order)) return '';

		$orders = array();
		foreach ($order as $f => $c) {
			$orders[] = '`'. $f .'` '. $c;
		}
		return ' ORDER BY ' . implode(',', $orders);
	}

	function &getOrder() {
		return $this->order;
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
		$group = $this->getGroup();
		if (empty($group)) return '';

		$orders = array();
		foreach ($group as $f) {
			$orders[] = '`'. $f .'` ';
		}
		return ' GROUP BY ' . implode(',', $orders);
	}

	function &getGroup() {
		return $this->group;
	}
	/**
	  * Returns the limit and offset SQL, in case they are set
	  */

	function limit() {
		if ($this->limit != 0)
			return ' LIMIT ' . $this->getLimit() . $this->offset();
	}

	function &getLimit() {
		return $this->limit;
	}
	/**
	  * Returns the offset SQL
	  */

	function offset() {
		return ' OFFSET ' . $this->getOffset();
	}

	function &getOffset() {
		return $this->offset;
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
		$n = null;
		$this->elements=& $n;
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
		if ($this->elements===null){
			$this->elements = array();
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

	function setDataType($dataType) {
		$this->dataType = $dataType;
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

class CompositeReport extends Report {
	var $report;

	function CompositeReport(&$report) {
		$this->report =& $report;
		parent::Report();
	}

	function &getTables() {
		return array_union_values($this->tables, $this->report->getTables());
	}

	function &getConditions() {
		return array_merge($this->conditions, $this->report->getConditions());
	}

	function &getGroup() {
		return array_merge($this->group,$this->report->getGroup());
	}

	function &getOrder() {
		return array_merge($this->order, $this->report->getOrder());
	}

	/*
	function &getLimit() {
		return array_merge($this->limit, $this->report->getLimit());
	}

	function &getOffset() {
		if ($this->offset == 0)
		return array_merge($this->offset, $this->report->getOffset());
	}
	*/

	function &getFields() {
		return array_merge($this->fields, $this->report->getFields());
	}

	function getDataType(){
		return $this->report->getDataType();
	}

	function setDataType($dataType) {
		$this->report->setDataType();
	}
}

function array_union_values() {
     //echo func_num_args();  /* Get the total # of arguements (parameter) that was passed to this function... */
     //print_r(func_get_arg());  /* Get the value that was passed in via arguement/parameter #... in int, double, etc... (I think)... */
     //print_r(func_get_args());  /* Get the value that was passed in via arguement/parameter #... in arrays (I think)... */

     $loop_count1 = func_num_args();
     $junk_array1 = func_get_args();
     $xyz = 0;

     for($x=0;$x<$loop_count1;$x++) {
       $array_count1 = count($junk_array1[$x]);

       if ($array_count1 != 0) {
           for($y=0;$y<$array_count1;$y++) {
             $new_array1[$xyz] = $junk_array1[$x][$y];
             $xyz++;
           }
       }
     }

     $new_array2 = array_unique($new_array1);  /* Work a lot like DISTINCT() in SQL... */

     return $new_array2;
}

class Condition {
	var $exp1;
	var $operation;
	var $exp2;

	function Condition($params) {
		$this->exp1 =& $params['exp1'];
		$this->exp2 =& $params['exp2'];
		$this->operation = $params['operation'];
	}

	function applyTo(&$report) {
		$e1 = $this->exp1->evaluateIn($report);
		$e2 = $this->exp2->evaluateIn($report);
		$report->setCondition($e1, $this->operation, $e2);
	}
}

class EqualCondition extends Condition {
	function EqualCondition($params) {
		$params['operation'] = '=';
		parent::Condition($params);
	}
}

class Expression {
	function Expression() {

	}
	function evaluateIn(&$report) {

	}
}

class ValueExpression extends Expression {
	var $value;

	function ValueExpression($value) {
		$this->value = $value;
		parent::Expression();
	}
	function evaluateIn(&$report) {
		return $this->value;
	}
}

class ObjectExpression extends Expression {
	var $object;

	function ObjectExpression(&$object) {
		$this->object =& $object;
		parent::Expression();
	}

	function evaluateIn(&$report) {
		return $this->object->getId();
	}
}

class PathExpression extends Expression {
	var $path;

	function PathExpression($path) {
		$this->path = $path;
		parent::Expression();
	}

	function registerPath(&$report) {
		$pp = explode('.', $this->path);
		$target = $pp[0];
		if (!isset($report->vars[$target])) {
			$o =& new $report->getDataType();
		}
		else {
			$o =& new $report->vars[$target];
			array_shift($pp);
		}

		//echo 'Setting path condition ' . print_r($pp, true) . '<br />';

		foreach ($pp as $index) {
			$class =& $o->$index->getDataType();
			$obj =& new $class;
			$report->addTables($obj->getTables());
			$otable = $o->tableForField($index);
			//echo 'Setting path condition: ' . $otable. '.' . $index, '=', $obj->getTable() . '.id<br />';
			$report->setCondition($otable. '.' . $index, '=', $obj->getTable() . '.id');
			$o =& $obj;
		}
		return $o;
	}
}

class AttrPathExpression extends PathExpression {
	var $attr;

	function AttrPathExpression($path, $attr) {
		$this->attr = $attr;
		parent::PathExpression($path);
	}

	function evaluateIn(&$report) {
		$o =& $this->registerPath($report);
		$otable = $o->tableForField($this->attr);
		$attr = $otable . '.' . $this->attr;
		return $attr;
	}
}

class ObjectPathExpression extends PathExpression {
	function evaluateIn(&$report) {
		$o =& $this->registerPath($report);
		$attr = $o->getTable() . '.id';
		return $attr;
	}
}



?>