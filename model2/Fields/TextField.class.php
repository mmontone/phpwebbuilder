<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class TextField extends DataField {
    function &visit(&$obj) {
        return $obj->visitedTextField($this);
    }

      function textField ($name, $isIndex) {
               parent::Datafield($name, $isIndex);
      }

     function SQLvalue() {
         return "'".$this->getValue()."'" . ", " ;
      }

}

class TextArea extends DataField {
      function textArea ($name, $isIndex) {
               parent::Datafield($name, $isIndex);
      }
    function &visit(&$obj) {
        return $obj->visitedTextArea($this);
    }



      function SQLvalue() {
         return "'".$this->getValue()."'". ", " ;
      }
}
?>