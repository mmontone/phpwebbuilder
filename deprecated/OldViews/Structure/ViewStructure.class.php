<?php

require_once (dirname(__FILE__)."/../View.class.php");

class ViewStructure extends View{
	var $obj;
	var $invalid_fields = array();
    function ViewStructure() {}
	/*
	 * Shows an entire object, with all of it's fields. 
	 * */
	function show(&$linker){
		return $this->showFields($linker, $this->obj->allFieldNames());
	}
	
	/*
	 * Shows the specified fields, with this type of link.    
	 * */
	function &fieldsMap(&$fields){  /* la variable indica si los campos que referencian a otros objetos se incluyen*/
		$ret = array();
		$obj =& $this->obj;
		$fs =& $obj->getFields($fields);
		for ($i=0; $i<count($fs) ; $i++) {
			$field =& $fs[$i];
			$showField =& $this->fieldShowObject($field);
			$ret[$field->colName]=& $showField->showMap($this);
		}
		return $ret;
 	}
 	function fieldsForm(&$linker, &$fields, $objFields){  /* la variable indica si los campos que referencian a otros objetos se incluyen*/
		$ret = "";
		$obj =& $this->obj;
		$fs =& $obj->getFields($fields);
		for ($i=0; $i<count($fs) ; $i++) {
			$field =& $fs[$i];
			$showField =& $this->fieldShowObject($field);
			$ret .= $showField->show($this, $linker, $objFields);
		}
		return $ret;
 	}
 	function headers(){return "";}
 	function footers(){return "";}
	
	function is_invalid_field(&$field) {
		for ($i=0; $i<count($this->invalid_fields) ; $i++) {
			$invalid_field =& $this->invalid_fields[$i];
			if ($field == $invalid_field) return true;
		}
		return false;
	}
	
	function declare_invalid_field(&$field) {
		$this->invalid_fields[]=&$field;
	}
}
?>