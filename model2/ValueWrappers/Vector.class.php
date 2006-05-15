<?php

class Vector extends ValueWrapper
{
    function Vector() {
       	$value = array();
    	for ($i = 0; $i < func_num_args(); $i++) {
    		$value[] =& func_get_arg($i);
    	}
    	parent::ValueWrapper($value);
    }

    function contents(&$object) {
    	return in_array($object, $this->value);
    }

    function &at($index) {
    	return $this->value[$index];
    }

    function put($index, &$object) {
    	$this->value[$index] =& $object;
    }

    function size() {
    	return count($this->value);
    }

    function set($index, &$object) {
    	$this->value[$index] =& $object;
    }

    function addFirst(&$object) {

    }

    function addLast(&$object) {
    	$this->value[] =& $object;
    }

    function isEmpty() {
    	return empty($this->value);
    }

    function &copy($from, $to) {
    	$vector =& new Vector;
    	$vector->value =& array_slice($this->value, $from, $from + $to);
    	return $vector;
    }
}

class ValueVector extends Vector
{
	function addLast(&$object) {
		$vector =& new ValueVector;
		$vector->value = $this->value; // Mal, cambiar: no quiero que me copie los objetos aca
		$vector->value[] =& $object;
	}
}

class VectorIterator
{

}

?>