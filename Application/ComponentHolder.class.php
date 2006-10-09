<?php

/* Should ComponentHolder be a Component decorator ? */
class ComponentHolder
{
	var $component;
	var $__owner_index;
	var $parent;
	var $realId =null;
	function ComponentHolder(&$component,&$owner_index, &$parent) {
	   $this->__owner_index = $owner_index;
	   $this->parent =& $parent;
	   $this->hold($component);
	}

	function &view(){
		return $this->parent->view;
	}
	function owner_index() {
		return $this->__owner_index;
	}

    function hold(&$component) {
    	$i = $this->owner_index();
	    $this->parent->$i=&$component;
		$component->holder =& $this;
		$this->component =& $component;
	}

    function getRealId(){
    	if ($this->realId===null)
	   		$this->realId = implode(array($this->parent->getId(),CHILD_SEPARATOR,$this->__owner_index));
    	return $this->realId;
    }
    function getSimpleId(){
    	return $this->__owner_index;
    }

    function &getComponent() {
    	return $this->component;
    }

}
?>