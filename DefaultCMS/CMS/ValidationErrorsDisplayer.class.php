<?php

class ValidationErrorsDisplayer extends Component {
	var  $error_msgs;

    function ValidationErrorsDisplayer(&$error_msgs) {
    	$this->error_msgs =& $error_msgs;
    	parent::Component();
    }

    function initialize() {
		foreach(array_keys($this->error_msgs) as $i) {
    		$this->addComponent(new Label($this->error_msgs[$i]), $i);
    	}
    }
}

?>