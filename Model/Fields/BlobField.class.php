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
   	   $rec = $this->owner->basicLoad();
   	   $this->getComplete = false;
   	   return $rec[$this->sqlName()];;
   }

}
?>
