<?php

class Report {
	/** Used with Collection Navigator.
	 * Should extend (or return) Collection.
	 * Elements should be descripted objects.
	 *
	 *
	 */
	 var $col;
	 var $mess;
	 function Report(&$col, $mess){
	 	$this->col =& $col;
	 	$this->mess =& $mess;
	 }
	 function &generate(){
	 	$self =& $this;
	 	$m =& $this->col->map($f = lambda('&$e', 'return $self->generateElement($e);', get_defined_vars()));
	 	delete_lambda($f);
	 	$m->fields = array_keys($this->mess);
	 	return $m;
	 }
	 function &generateElement(&$e){
	 	$o =& new DescriptedObject();
	 	foreach($this->mess as $n=>$m){
	 		$o->$n =& new ValueHolder(apply_messages($e, $m));
	 	}
	 	$o->originalData =& $e;
	 	return $o;
	 }
}
?>