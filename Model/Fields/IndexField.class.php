<?php
require_once dirname(__FILE__) . '/NumField.class.php';
require_once dirname(__FILE__) . '/../PersistentCollection.class.php';

class IndexField extends NumField {
      var $collection;
      var $nullValue;
      function IndexField ($name, $isIndex, $dataType, $nullValue="") {
         parent::Numfield($name, $isIndex);
         $this->collection =& new PersistentCollection($dataType);
         $this->nullValue =& $nullValue;
      }
    function &visit(&$obj) {
        return $obj->visitedIndexField($this);
    }
    function &obj() {
        return $this->collection->getObj($this->getValue());
    }
    function viewValue() {
        $obj =& $this->obj();
        return $obj->indexValues();
    }
    function getValue(){
    	return $this->value + 0;
    }
}

class UserField extends IndexField {
      function UserField ($name, $isIndex, $dataType, $nullValue="") {
         parent::IndexField($name, $isIndex, $dataType, $nullValue="");
      }
    function &visit(&$obj) {
        return $obj->visitedUserField($this);
    }

}

?>