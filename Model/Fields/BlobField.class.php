<?php

class BlobField extends DataField {
   function &visit(&$obj) {
		return $obj->visitedBlobField($this);
   }
   function SQLvalue() {
     $value = $this->getValue();
     return "'$value', " ;
   }
}
?>
