<?php

class ValidationErrorsDisplayer extends Component {

    function ValidationErrorsDisplayer(&$error_msgs) {
    	$this->error_msgs =& $error_msgs;
    }

    function initialize() {
    	foreach(array_keys($this->error_msgs) as $i) {
    		$this->addComponent(new Label($this->error_msgs[$i]));
    	}
    }
}
?>