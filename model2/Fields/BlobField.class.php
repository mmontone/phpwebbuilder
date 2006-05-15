<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class BlobField extends DataField {
   function BlobField ($name, $isIndex) {
               parent::DataField($name, $isIndex);
   }
   function &visit(&$obj) {
		return $obj->visitedBlobField($this);
   }
   function SQLvalue() {
     return "'$this->value'" . ", " ;
   }
}
?>
