<?php

class ReportVar {
	var $id;
    var $class;
    var $prefix;

    function ReportVar($params) {
	   $this->id = $params['id'];
       $this->class = $params['class'];
       $this->prefix = $params['prefix'];
	}
}

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
	  var $sqls = array();
      var $evaluated=false;
      var $parent = null;
      var $target_var=null;


	function Report($params=array()) {
		$this->initializeSelectExp();
		parent::Collection();
		$this->setConfigArray($params);
	}

    function &getTargetVar() {
    	//print_backtrace('Returning target var: ' . print_r($this->target_var,true) . ' in ' . $this->printString());
        return $this->target_var;
    }
	function evaluateAll(){
		$this->inSQL();
		$this->selectsql();
	}
    function &getVar($id) {
        //print_backtrace('Looking for variable: ' . $id . ' in ' . $this->printString());
        if (isset($this->vars[$id])) {
        	//print_backtrace('Found: ' . $id . ' in ' . $this->printString());
            return $this->vars[$id];
        }
        else {
        	if ($this->parent !== null) {
        		return $this->parent->getVar($id);
        	}
            else {
                //print_backtrace('Variable: ' . $id  . ' not found in ' . $this->printString());
				$n=null;
            	return $n;
            }
        }
    }

    function setTargetVar($var, $type) {
    	//print_backtrace('Setting target var: ' . $var . ' in ' . $this->printString());
        $v =& $this->primDefineVar($var, $type);
        $this->target_var =& $v;
        $this->dataType = $type;
    }

    function setConfigArray($params){
		if (isset($params['target'])){
            $this->setTargetVar($params['target'], $params['class']);
        } else {
            if (isset($params['class'])){
    			$this->setDataType($params['class']);
    		}
        }

		if (isset($params['fields'])){
			$this->fields =$params['fields'];
		}
		if (isset($params['sqls'])){
			$this->sqls =$params['sqls'];
		}
		if (isset($params['from'])){
			foreach($params['from'] as $var=>$class){
				$this->defineVar($var,$class);
			}
		}
		if (isset($params['exp'])){
			$this->setSelectExp($params['exp']);
		}
		if (isset($params['limit'])){
			$this->setLimit($params['limit']);
		}
	}

    function initializeSelectExp() {
        $this->select_exp =& new AndExp;
    }

	function addTable($table) {

        $this->tables = array_union_values($this->tables, array($table));
        //echo 'Tables: ' . $this->printString() . ':'.print_r($this->tables,true) . '<br/>';
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
		//print_backtrace('Report: ' . $this->printString() . ' setting condition: ' . $field . $comparator . $value);
		$target_var = $this->getTargetVar();
        $cond =& new Condition(array('operation'=> $comparator,
				'exp1' => new AttrPathExpression($target_var->id, $field),
				'exp2'=> new ValueExpression($value)));
		$cond->evaluateIn($this);
		$this->select_exp->addExpression($cond);
		//$this->conditions[]=array($this->parseField($field),$comparator,$value);
		$n = null;
		$this->elements=& $n;
	}

    function getTargetTable() {
    	$var =& $this->getTargetVar();
        $dt = $var->class;
        $obj =& PersistentObject::getMetaData($dt);
        return $obj->getTablePrefixed($var->prefix);
    }

	function setConditions($conditions) {
		foreach ($conditions as $condition) {
			$this->setCondition($condition[0], $condition[1], $condition[2]);
		}
	}

	function &defineVar($id, $class) {
		$o =& PersistentObject::getMetaData($class);
        $this->addTables($o->getTablesPrefixed($id . '_'));
        return $this->primDefineVar($id, $class);
	}

    function &primDefineVar($id, $class) {
    	$this->vars[$id] =& new ReportVar(array('id' => $id, 'class' => $class, 'prefix' => $id . '_'));
        return $this->vars[$id];
    }


	function setPathCondition(&$condition) {
		//print_backtrace($this->printString() . ' setting path condition: ' . print_r($condition,true));
		$this->select_exp->addExpression($condition);
		unset($this->sqls['where']);
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
		if (!isset($this->sqls['where'])){
			$select_exp =& $this->getSelectExp();
			$printed = $select_exp->printString();

			if ($printed == '') {
			  $cond = ' ';
			}
			else {
			  $cond = ' WHERE ' . $printed;
			}
			$this->sqls['where'] = $cond;
		}
		return $this->sqls['where'];
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
		if (!isset($this->sqls['in_fields'])){
			$meta =& $this->getMetaData();
			$var =& $this->getTargetVar();
			$this->sqls['in_fields'] = $meta->fieldNamePrefixed('id','SELECT', $var->prefix);
		}
		$sql = 'SELECT '.  $this->sqls['in_fields'].' FROM ' . $this->restrictions().$this->group(). $this->order() . $this->limit();
        return $sql;
	}
	function size() {
		$db = & DBSession::Instance();
		$reg = $db->query($this->sizeSQL(),true);
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
		$meta =& $this->getMetaData();
		$arr = array_merge($meta->allIndexFieldNames(), $this->getFields());
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
	function &getMetaData(){
		return PersistentObject::getMetaData($this->getDataType());
	}
	/**
	  * Returns all the field names, backquote encapsed
	  */
	function fieldNames(){
		if (!isset($this->sqls['fields'])){
			foreach($this->getFields() as $f=>$n){
				if (!is_numeric($f)){
					$ret []= $f .' as `'. $n.'`';
				} else {
					$ret []= '`'. $this->parseField($n).'`';
				}
			}
			if ($this->getDataType()!='PersistentObject'){
	            $meta =& $this->getMetaData();
	            $var =& $this->getTargetVar();
	            $ret []= $meta->fieldNamesPrefixed('SELECT', $var->prefix);
	        }
			$this->sqls['fields'] = implode(',',$ret);
		}
		return $this->sqls['fields'];
	}

    function freeVar() {

    }

	function &getFields() {
		return $this->fields;
	}

	function select($string) {
		$this->sqls['fields'] = $string;
	}

	/**
	  * Returns the tables to be used
	  */
	function tableNames(){
		if (!isset($this->sqls['tables'])){
			$this->sqls['tables'] = implode(',', $this->getTables());
		}
		return $this->sqls['tables'];
	}

	function getTables() {
	    $target =& $this->getTargetVar();
	    if (is_object($target)){
	        $datatype = $target->class;
	        $obj =& PersistentObject::getMetaData($datatype);
            return array_union_values($obj->getTablesPrefixed($target->prefix), $this->tables);
	    } else {
	    	return $this->tables;
	    }
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

    function setLimit($limit) {
    	$this->limit = $limit;
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

    function setOffset($offset) {
    	$this->offset = $offset;
    }
	/**
	  * Returns the complete query for filling the report
	  */

	function selectsql(){
		$this->evaluateSelect();
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
		$conds = $this->conditions();
		return $this->tableNames() .$conds;
	}
	/**
	  * Returns an array of all the elements (considering limit and offset)
	  */

	function &elements() {
		if ($this->elements===null){
			$this->elements = array();
			$sql = $this->selectsql();
			$db =& DBSession::Instance();
			#@php4
				$reg =& $db->query($sql,true);
				if (is_exception($reg)) return $reg;
			//@#
			#@php5
				try{
					$reg = $db->query($sql,true);
				} catch(Exception $e){
					return $reg;
				}
			//@#
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
        $this->setTargetVar('target', $dataType);
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
		$obj =& PersistentObjectMetaData::getMetaData($dt);
		return $this->fillExtras($obj->loadFromRec($data), $data);
	}
	function getDataTypeSqlId(){
		if(!isset($this->dataTypeSqlId)){
			$dt = $this->getDataType();
			$obj =& PersistentObjectMetaData::getMetaData($dt);
			$this->dataTypeSqlId =$obj->getIdSQLName();
		}
		return $this->dataTypeSqlId;
	}
	function &fillExtras(&$obj,$data){
		$md =& PersistentObjectMetaData::getMetaData(getClass($obj));
		$ret = $md->fieldNames('SELECT');
	 	foreach($data as $n=>$m){
	 		if (!isset($obj->$n) || !is_a($obj->$n, 'DataField')){
	 			$obj->$n =& new ValueHolder($m);
	 		}
	 	}
	 	return $obj;
	}
	function printString(){
        $vars = array();
        foreach ($this->vars as $var) {
        	$vars[] = $var->id . ':' . $var->class;
        }

        return $this->primPrintString('(' . $this->getDataType() . ') vars: ' . implode(',', $vars));
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
if (Compiler::compileOpt('recursive')){
	return '
		function select($query){
	        $oc =& new OQLCompiler;
			$res = $oc->fromQuery($query);
			if ($oc->error!=null || $res==\'\') print_backtrace_and_exit($oc->error);
	        return $res;
		}';
} else {
	return '
		function select($query){
	        $oc =& new OQLCompiler;
			$res = $oc->fromQuery($query);
			if ($oc->error!=null || $res==\'\') print_backtrace_and_exit($oc->error);
	        return $res;
		}';
}
//@#

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