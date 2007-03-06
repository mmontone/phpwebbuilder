<?php

class BlobField extends DataField {
	var	$getComplete = false;
   function &visit(&$obj) {
		return $obj->visitedBlobField($this);
   }
   function SQLvalue() {
     $value = $this->getValue();
     return "'$value', " ;
   }
   function setValue($v){
   	   parent::setValue($v);
   	   $this->getComplete = ($v!==null);
   }
   function &getCompleteValue(){
   	   $this->getComplete = true;
   	   $r =& new Report();
   	   $r->fields[$this->colName] = 'data';
   	   $r->defineVar('o', getClass($this->owner));
   	   $r->setPathCondition(new EqualCondition(array('exp1'=>new ObjectPathExpression('o'),'exp2'=>new ObjectExpression($this->owner))));
   	   $elem =& $r->first();
   	   return $elem->data->getValue();
   }

}
?>
