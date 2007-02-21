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
	 var $dataType = 'PersistentObject';
	 /**
	  * Adds a condition to filter the data
	  */

	  var $group = array();
	  var $vars = array();
	  var $select_exp;
	  var $select = null;
      var $evaluated=false;


	function Report($params=array()) {
		$this->initializeSelectExp();
		parent::Collection();
		if (isset($params['class'])){
			$this->dataType =$params['class'];
		}
		if (isset($params['fields'])){
			$this->fields =$params['fields'];
		}
		if (isset($params['from'])){
			foreach($params['from'] as $var=>$class){
				$this->defineVar($var,$class);
			}
		}
		if (isset($params['exp'])){
			$this->setSelectExp($params['exp']);
		}
	}

    function initializeSelectExp() {
        $this->select_exp =& new AndExp;
    }

	function addTable($table) {
		$this->tables = array_union_values($this->tables, array($table));
	}

	function setTables($tables) {
		$this->tables = $tables;
	}

	function addTables($tables) {
		foreach ($tables as $table) {
			$this->addTable($table);
		}
	}

	function setCondition($field, $comparator, $value) {
		//echo 'Report: Setting condition: ' . $field . $comparator . $value . '<br />';
		$cond =& new Condition(array('operation'=> $comparator,
				'exp1' => new ValueExpression('`' . $this->parseField($field) . '`'),
				'exp2'=> new ValueExpression($value)));
		$cond->evaluateIn($this);
		$this->select_exp->addExpression($cond);
		//$this->conditions[]=array($this->parseField($field),$comparator,$value);
		$n = null;
		$this->elements=& $n;
	}

	function setConditions($conditions) {
		foreach ($conditions as $condition) {
			$this->setCondition($condition[0], $condition[1], $condition[2]);
		}
	}

	function defineVar($id, $class) {
		$o =& new $class(array(),false);
		$this->addTables($o->getTables());
		$this->freeVar($id,$class);
	}
	function registerAllVars(&$report){
		foreach($this->vars as $id=>$class){
			$report->freeVar($id, $class);
		}
	}
	function freeVar($id, $class) {
		$this->vars[$id] =& $class;
	}

	function setPathCondition(&$condition) {
		//print_backtrace('Setting path condition: ' . print_r($condition,true));
		$this->select_exp->addExpression($condition);
        $this->evaluated=false;
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

		/*
		$cond = '1=1';
		foreach ($this->getConditions() as $c) {
			$cond .= ' AND `'. $c[0] .'` '. $c[1] .' '. $c[2];
		}
		$cond = ' WHERE ' . $cond;*/

		$select_exp =& $this->getSelectExp();
		$printed = $select_exp->printString();

		if ($printed == '') {
		  $cond = ' ';
		}
		else {
		  $cond = ' WHERE ' . $printed;
		}
		return $cond;
	}

	function &getConditions() {
		return $this->conditions;
	}
	function evaluateSelect(){
		if (!$this->evaluated) {
            $this->select_exp->evaluateIn($this);
            $this->evaluated=true;
        }
	}
	function &getSelectExp() {
		$this->evaluateSelect();
		return $this->select_exp;
	}

	function setSelectExp(&$exp) {
		$this->select_exp =& $exp;
        $this->evaluated=false;
	}
	/**
	  * Returns the size of the collection
	  */
	function sizeSQL(){
		$sql = 'SELECT COUNT(';
		$g = $this->group();
		if ($g!=''){
			$sql .= 'DISTINCT '.substr($g, 10);
		}else{
			$sql .= '*';
		}
		$sql .=') as collection_size FROM ' . $this->restrictions();
		return $sql;
	}
	function inSQL(){
		$obj =& $this->getObject();
		$sql = 'SELECT '.substr($obj->id->fieldName('SELECT'), 0, -2).' FROM ' . $this->restrictions().$this->group(). $this->order() . $this->limit();
        return $sql;
	}
	function size() {
		$db = & DBSession::Instance();
		$reg = $db->query($this->sizeSQL());
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
	  * Returns a New object of the dataype
	  */
	function &getObject(){
		$dt = $this->getDataType();
		$o  =& new $dt(array(),false);
		return $o;
	}
	/**
	  * Returns all the field names, backquote encapsed
	  */
	function fieldNames(){
		if ($this->select !== null) {
			return $this->select;
		}

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

	function select($string) {
		$this->select = $string;
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

	function getTables() {
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
		if ($this->getLimit() != 0)
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
        //$this->triggerEvent('refreshed', $this);
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
			$db =& DBSession::Instance();
			$reg = $db->SQLExec($sql, FALSE, $this);
			if ($reg===false) return false;
			while ($data = $db->fetchrecord($reg)) {
				$this->addElement($this->makeElement($data));
			}
		}
		return $this->elements;
	}

	function addElement(&$element) {
		$this->elements[] =& $element;
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
		$dt = $this->getDataType();
		$id = @$data[$this->getDataTypeSqlId()];
		$old =& PersistentObject::findGlobalObject($dt,$id);
		if ($old!==null){
			$old->loadFrom($data);
			return $this->fillExtras($old, $data);
		}
		$obj =& new $dt(array(),false);
		return $this->fillExtras($obj->loadFromRec($data), $data);
	}
	function getDataTypeSqlId(){
		if(!isset($this->dataTypeSqlId)){
			$dt = $this->getDataType();
			$obj =& new $dt(array(),false);
			$this->dataTypeSqlId =$obj->id->sqlName();
		}
		return $this->dataTypeSqlId;
	}
	function &fillExtras(&$obj,$data){
		$ret = $obj->fieldNames('SELECT');
	 	foreach($data as $n=>$m){
	 		if (!isset($obj->$n) || !is_a($obj->$n, 'DataField')){
	 			$obj->$n =& new ValueHolder($m);
	 		}
	 	}
	 	return $obj;
	}
	function printString(){
		return $this->primPrintString('('.$this->getDataType().')');
	}

    /*
    function add(&$element) {
    	print_backtrace('Adding ' . $element->printString() . ' to ' . $this->printString());
        #@gencheck
        if (!is_a($element, $this->getDataType())) {
            print_backtrace('Warning: ' . $element->printString() . ' is not of type ' . $this->getDataType());
        }//@#

        $c =& new EqualCondition(array('exp1' => new ValueExpression('`' . $element->getTable() . '`.id'),
                                       'exp2' => new ValueExpression($element->getId())));
        $c->evaluateIn($this);

        $exp =& new OrExp;
        $exp->addExpression($c);
        $exp->addExpression($this->getSelectExp());

        $this->select_exp =& $exp;
        $this->changed();
    }

    function remove(&$element) {
        print_backtrace('Removing ' . $element->printString() . ' from ' . $this->printString());
        #@gencheck
        if (!is_a($element, $this->getDataType())) {
            print_backtrace('Warning: ' . $element->printString() . ' is not of type ' . $this->getDataType());
        }//@#

        $c =& new Condition(array('exp1' => new ValueExpression('`' . $element->getTable() . '`.id'),
                                  'operation' => '<>',
                                  'exp2' => new ValueExpression($element->getId())));
        $c->evaluateIn($this);

        $exp =& new AndExp;
        $exp->addExpression($c);
        $exp->addExpression($this->getSelectExp());

        $this->select_exp =& $exp;
        $this->changed();
    }*/
}
#@preprocessor
//compile _once(dirname(__FILE__).'/OQLCompiler.class.php');
Compiler::usesClass(__FILE__,'OQLCompiler');

if (!function_exists('select')){
	function select($query){
		$oc =& new OQLCompiler;
		return $oc->fromQuery($query,get_defined_vars());
	}
}
@#

/*

 select [<class>][({<field> as <ident>[,]})]
 	[from {<ident> : <class>[,]}]
 	[where <expression>]

 #//@select Tema (a as a)
     from tema : Tema
     where tema.titulo LIKE $titulo and
              tema.reunion.publica = 1 @#
 #//@select Tema (count as temas)
     from tema : Tema,reunion:Reunion
     where tema.titulo LIKE $titulo and
              tema.reunion.publica = TRUE@#
 */

?>