<?
/*
require_once dirname(__FILE__) . '/DataField.class.php';

class embedField extends IndexField {
      var $obj;
      var $dataType;
      function embedField ($name, $isIndex, $dataType) {
         parent::IndexField ($name, $isIndex, $dataType, "");
         $this->collection = new PersistentCollection($dataType);
         $this->nullValue = "";
         $this->dataType = $dataType;
      }
	function loadFrom($reg){
	    parent::loadFrom($reg);
	    $this->obj = new $this->dataType;
	    $this->obj->setID($this->value);
	    $this->obj->loadFrom($reg);
    }
	function visit($obj) {
		return $obj->visitedEmbedField($this);
	}
}
*/

?>