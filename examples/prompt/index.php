<?php

class PromptTest extends Component 
 {
 	var $message;
 	
 	function start() {
 		$this->read_input();
 	}
 	
 	function read_input() {
 		$this->call(new PromptDialog("Escribi algo: ", array('on_accept' => 'show_message')));
 	}
 	
 	function show_message($params) { 		
		$this->message = $params['text']; 		
 	}
 	
 	function render_on(&$out) {
 		$out .= "<h1>" . $this->message . "</h1>";
 	}
 }
  
class PromptApplication extends Application 
 {
 	function &set_root_component() {
 		return new PromptTest;
 	}
 }
 
 
 
 
?>