<?

require_once dirname(__FILE__) . '/TextField.class.php';

class PasswordField extends TextField  {
	function &visit(&$obj) {
		return $obj->visitedPasswordField($this);
	}
      function loadFrom($form){
	  	$name = $this->colName;
		if (isset($form[$name."1"])) {
			if ($form[$name."1"] == $form[$name."2"]){
				if ($form[$name."1"] == "") {
					$this->setValue("");
				} else {
					$val = $this->trim($form[$name."1"]);
					$this->setValue($val);
				}
	  		}
		}
		return true;
      }
      function SQLvalue() {
   		if ($this->getValue() == "") {return "";}
    	else {
         	return "'".$this->getValue()."'" . ", " ;
    	}
      }
    function fieldName ($operation) {
	    	if ($this->getValue() == "") {
	    		return "";
	    	} else {
      		return $this->colName . ", ";
    		}
    }
    function updateString() {
	    	if ($this->getValue() == "") {return "";} else {
	       		return $this->colName . " = ". $this->SQLvalue();
	    	}
    }
}
?>