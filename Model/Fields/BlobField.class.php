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
   function &getCompleteValue(){
   	   $r =& new Report();
   	   $r->fields[$this->colName] = 'data';
   	   $r->defineVar('o', getClass($this->owner));
   	   $r->setPathCondition(new EqualCondition(array('exp1'=>new ObjectPathExpression('o'),'exp2'=>new ObjectExpression($this->owner))));
   	   $elem =& $r->first();
   	   return $elem->data->getValue();
   }
   function fieldNamePrefixed ($operation, $pfx) {
        if ($operation !== 'SELECT') {
            return parent::fieldNamePrefixed ($operation, $pfx);
        }
    }
    function updateString() {
		if ($this->getValue() == "") {
			return "";
		} else {
			return $this->colName . " = " . $this->SQLvalue();
		}
	}

}
?>
