<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class BoolField extends DataField {
      function boolField ($name, $isIndex) {
          parent::Datafield($name, $isIndex);
      }
      function SQLvalue() {
         return $this->getValue(). ", " ;
      }
    function loadFrom($form) {
      $val = $form[$this->colName];
      if ($val==="false") $val = 0;
      if ($val==="true") $val = 1;
      if ($val==null) $val = 0;
      $this->setValue($val);
      return $this->check($val);
    }
    function &visit(&$obj) {
        return $obj->visitedBoolField($this);
    }
}
?>