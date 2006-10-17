<?php

class JoinedPersistentCollection extends PersistentCollection{
	function JoinedPersistentCollection($dataType, $table, $field){
		parent::PersistentCollection ($dataType);
		$this->innerTable = $table;
		$this->innerField = $field;
		$this->setCondition($table.'.'.$field, '=',$this->tableName().'.id');
	}
	function tableNames() {
		$obj = new $this->dataType;
		return $obj->tableNames(). ', '.$this->innerTable;
	}
}
?>