<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class CollectionField extends DataField {
    var $collection;
    var $fieldname;
    function fieldName () {}
    function &visit(&$obj) {
        return $obj->visitedCollectionField($this);
    }
    function CollectionField ($name, $dataType) {
       parent::DataField($dataType.$name, FALSE);
       $this->collection =& new PersistentCollection($dataType);
       $this->fieldname = $name;
       $this->collection->conditions[$this->fieldname]= array("=", "0");
    }
    function setID($id) {
        $this->setValue($id);
        $this->collection->conditions[$this->fieldname] = array("=",$id);
    }
    function SQLvalue () {}
    function updateString() {}
    function loadFrom() {}
    function &objects(){return $this->collection->objects();}
    function canDelete() {
        $arr =& $this->collection->objects();
        $can = count($arr)==0;
        if (!$can) trace("The ".$this->colName." collection is not empty<BR>\n");
        return $can;
    }
}

?>
