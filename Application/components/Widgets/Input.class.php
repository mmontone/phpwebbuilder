<?php

class Input extends Widget {
    var $hidden;
	function printValue(){
		return $this->value_model->getValue();
	}
    function isHidden(){
        return $this->hidden;
    }
    function beHidden($bool){
        return $this->hidden = $bool;
    }
}

?>