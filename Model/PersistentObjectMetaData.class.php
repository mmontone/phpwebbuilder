<?php

class PersistentObjectMetaData {
	var $className;
	var $fields = array();

	function PersistentObjectMetaData($class){
		$this->className = $class;
		$this->createObject(false);
	}
	function createObject($create=true){
		$class = $this->className;
		$meta =& new $class(array(), false, true);
		$this->class =& $meta;
    	$meta->metadata =& $this;
		if ($create) $this->class->basicInitialize();
	}
	function disposeObject(){
		//unset($this->fields);
		unset($this->class);
	}
	function initialize(){
		$this->class->basicInitialize();
		$this->cacheAllData();
	}
	function &getMetaData($class){
		$metadata =& getSessionGlobal('persistentObjectsMetaData');
		$class = strtolower($class);
		if (!isset($metadata[$class])){
			$metadata[$class] =& new PersistentObjectMetaData($class);
			$metadata[$class]->initialize();
		}
		return $metadata[$class];
	}
	function cacheAllData(){
		$this->fieldNames=$this->class->fieldNames;
		$this->fields=$this->class->fields;
		$this->indexFields=$this->class->indexFields;
		$this->indexFieldsLevel=$this->class->indexFieldsLevel;
		$this->table=$this->class->table;
		foreach($this->class->fieldNames as $f){
			$this->colNames[$f] = $this->class->$f->colName;
			if (is_a($this->class->$f, 'indexfield')){
				$this->dataTypes[$f] = $this->class->$f->getDataType();
			}
			$this->sqlname[$f]=$this->class->$f->sqlName();
	    	$this->selectfieldnameprefixed[$f] = $this->class->$f->fieldNamePrefixed('SELECT', '{$prefix}');
    		$this->othersfieldnameprefixed[$f] = $this->class->$f->fieldNamePrefixed('INSERT', '{$prefix}');
    		unset($this->class->$f->owner);
		}
		$this->fieldNames('SELECT');
		$this->fieldNames('UPDATE');
		$this->getTables();
		$this->disposeObject();
		/*if (DescriptedObject::isNotTopClass($this->className)){
			header('Content-type: text/plain');
			print_r($this);exit;
		}*/
	}
	/**
	 * Returns all the field names
	 */
	function allFieldNames() {
		return $this->fieldNames;
	}
	/**
	 * Returns all the index field's names
	 */
	function allIndexFieldNames() {
		return $this->indexFields;
	}
	function allIndexFieldNamesThisLevel() {
		return (array)$this->indexFieldsLevel[$this->className];
	}
	/**
	 * Returns this level's field names (inheritance-wise)
	 */
	function allFieldNamesThisLevel() {
		return array_keys($this->fields[$this->className]);
	}
    /**
	 * Returns a SQL string for accesing the fields for the specified operation
	 */
    function fieldNames($operation) {
    	return $this->fieldNamesPrefixed($operation, 'target_');
    }
	/**
	 * Gets all the fields of all the levels for the SQL query
	 */
	function &allFieldNamesAllLevels(){
		$rcs = get_related_classes($this->className);
		$fs = array();
		foreach($rcs as $rc){
			if ($rc != 'persistentobject' && $rc != 'descriptedobject' && $rc != 'pwbobject') {
				$o = PersistentObject::getMetaData($rc);
				$fs = array_merge($fs, $o->allSQLFieldNames());
			}
		}
		$fs = array_merge($fs, $this->allSQLFieldNames());
		return $fs;
	}
		/**
	 * Returns an array of the sql names of the fields (all levels)
	 */
	function & allSQLFieldNames() {
		$arr = array ();
		foreach ($this->allFieldNamesThisLevel() as $name) {
			$arr[$this->sqlname[$name]] = $this->fieldNamePrefixed($name, 'SELECT', '{$prefix}');
		}
		return $arr;
	}
    function fieldNamesPrefixed($operation, $prefix) {
		if ($operation=='SELECT'){
			if (!isset($this->allFieldsNamesAllLevels)){
				$fieldnames = array();
				$fs =& $this->allFieldNamesAllLevels();
		        foreach ($fs as $fn) {
		            if ($fn != null) {
		            	$fieldnames[] = $fn;
		            }
				}
		        $this->allFieldsNamesAllLevels=  implode(',', $fieldnames);
			}
			$fieldsNames = eval('return "'.$this->allFieldsNamesAllLevels.'";');
		} else {
			if (!isset($this->allFieldsNamesThisLevel)){
				$fieldnames = array();
				$fs =& $this->allFieldNamesThisLevel();
		        foreach ($fs as $field) {
				    $fn = $this->fieldNamePrefixed($field, $operation, '{$prefix}');
		            if ($fn != null) {
		            	$fieldnames[] = $fn;
		            }
				}
		        $this->allFieldsNamesThisLevel=  implode(',', $fieldnames);
			}
			$fieldsNames = eval('return "'.$this->allFieldsNamesThisLevel.'";');
		}
		return $fieldsNames;
    }
    function fieldNamePrefixed($field, $operation, $prefix) {
    	if ($operation=='SELECT'){
    		$f = $this->selectfieldnameprefixed[$field];
    	} else {
    		$f = $this->othersfieldnameprefixed[$field];
    	}
    	return eval('return "'.$f.'";');
    }
	/**
	 * Returns the table name for the object.
	 */
	function tableName() {
		return $this->tableNamePrefixed('');
	}

    function tableNamePrefixed($prefix,$table=null) {
    	return '`' . $this->getTablePrefixed($prefix,$table) . '`';
    }
	function getTable() {
		return $this->getTablePrefixed('target_');
	}

    function getTablePrefixed($prefix,$table=null) {
        return constant('baseprefix') . $prefix . (($table==null)?$this->table:$table);
    }

	/**
	 * Returns the table names for the object (including all
	 * hierarchy that involves it)
	 */
	function getTables() {
		return $this->getTablesPrefixed('target_');
	}

    function getTablesPrefixed($prefix) {
        if (!isset($this->allObjectTables)){
	        $tns[] = $this->tableName() . ' AS ' . $this->tableNamePrefixed('{$prefix}');
	        $p0 = $this->className;
	        $pcs = get_superclasses($p0);
	        $o0 =& $this;
	        foreach($pcs as $pc){
	            if ($pc != 'persistentobject' && $pc != 'descriptedobject' && $pc != 'pwbobject' && $pc != ''){
	            	$o1 =& PersistentObject::getMetaData($pc);
	                $tns[] = 'LEFT OUTER JOIN '.$o1->tableName().' AS ' . $o1->tableNamePrefixed('{$prefix}') . ' ON '. $o1->tableNamePrefixed('{$prefix}').'.id = '.$o0->tableNamePrefixed('{$prefix}').'.super';
	            } else {
	            	break;
	            }
	            $o0 =& $o1;
	            $p0 = $pc;
	        }
	        $scs = get_subclasses($this->className);
	        foreach($scs as $sc){
	            $o1 =& PersistentObject::getMetaData($sc);
	            $pc = get_parent_class($sc);
	            $o2 =& PersistentObject::getMetaData($pc);
	            if ($pc != 'persistentobject' && $pc != 'descriptedobject' && $pc != 'pwbobject' && $pc != ''){
	                $tns[] = 'LEFT OUTER JOIN '.$o1->tableName(). ' AS ' . $o1->tableNamePrefixed('{$prefix}') . ' ON '. $o2->tableNamePrefixed('{$prefix}').'.id = '. $o1->tableNamePrefixed('{$prefix}').'.super';
	            } else {
	            	break;
	            }
	        }
	        $this->allObjectTables = implode(' ',$tns);
        }
        $tableNames = '';
        $tableNames = eval('return "'.$this->allObjectTables.'";');
        return array($tableNames);
    }


	function tableNames(){
		$tns = $this->getTables();
		return $tns[0];
	}
	function tableForField($field) {
		return $this->tableForFieldPrefixed($field, '');
	}

    function tableForFieldPrefixed($field, $prefix) {
		$o1 =& $this;

		if (in_array($field, $o1->allFieldNamesThisLevel())) {
			return $o1->getTablePrefixed($prefix);
		}

		$p0 = $o1->className;
		$pcs = get_superclasses_upto($p0, 'PersistentObject');
        foreach($pcs as $pc){
			$o1 =& PersistentObject::getMetaData($pc);

			if (in_array($field, $o1->allFieldNamesThisLevel())) {
				return $o1->getTablePrefixed($prefix);
			}
		}

        print_backtrace_and_exit('Error: field not found ' . $field . ' in ' . $this->className);
	}
	/**
	 * Loads an object from a database record
	 */
	function &loadFromRec(&$rec){
		$obj =& $this->chooseSubclass($rec);
		$obj->loadFrom($rec);
		$obj->attachFieldsEvents();
		$obj->initializeObject();
		return $obj;
	}
	/**
	 * Chooses the right subclass for the object
	 */
	function &chooseSubclass(&$rec){
		$c = $this->className;
		$rcss = get_subclasses($c);
		$rcs = array_reverse($rcss);
		foreach($rcs as $rc){
			$o =& PersistentObjectMetaData::getMetaData($rc);
			if ($o->canBeLoaded($rec)){
				$o =& new $rc(array(), false);
				return $o;
			}
		}
		$o =& new $c(array(), false);
		return $o;
	}
	/**
	 * Checks if the subclass can be loaded from the record
	 */
	function canBeLoaded(&$rec){
		return isset($rec[$this->getIdSQLName()]);
	}
	function getColName($field){
		return $this->colNames[$field];
	}
	function getDataType($field){
		return$this->dataTypes[$field];
	}
	function getIdSQLName(){
		return $this->sqlname['id'];
	}

	function printString() {
		return '[Metadata of ' . $this->className . ']';
	}

}
?>