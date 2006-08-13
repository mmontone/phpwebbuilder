<?php

/* Should ComponentHolder be a Component decorator ? */
class ComponentHolder
{
	var $component;
	var $__owner_index;
	var $parent;
	var $realId;
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

    function &copy_for_backtracking() {
        /* PHP4 */
        $my_copy = $this;
        $my_copy->component = $this->component->copy_for_backtracking();
        return $my_copy;
    }
    function getRealId(){
    	if (!$this->realId)
	   		$this->realId = implode(array($this->parent->getId(),"/",$this->__owner_index));
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