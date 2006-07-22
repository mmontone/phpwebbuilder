<?php

require_once dirname(__FILE__) . '/DataField.class.php';

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
