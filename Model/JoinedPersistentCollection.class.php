<?php

class JoinedPersistentCollection extends PersistentCollection{
	function JoinedPersistentCollection($dataType, $table, $field){
		parent::PersistentCollection ($dataType);
		$this->innerTable = $table;
		$this->innerField = $field;
		$this->conditions[$table.'.'.$field] = array('=',$this->tableName().'.id');
	}
	function tableNames() {
		$obj = new $this->dataType;
		return $obj->tableNames(). ', '.$this->innerTable;
	}
}
?>