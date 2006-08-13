<?

class EnumField extends TextField {
      var $vals;
    function enumField ($name, $isIndex=null, $vals=null) {
    	 if (!is_array($name) && !is_array($isIndex)) {
         	parent::TextField (array('fieldName'=>$name, 'is_index'=>$isIndex, 'values'=>$vals));
    	 } else {
    	 	if (is_array($name)){
    	 		parent::TextField($name);
    	 	} else {
    	 		$isIndex['fieldName'] = $name;
    	 		parent::TextField($isIndex);
    	 	}
    	 }
    }
	function createInstance($params){
		parent::createInstance($params);
		$this->vals = $params['values'];
	}
	function &visit(&$obj) {
		return $obj->visitedEnumField($this);
	}
}
