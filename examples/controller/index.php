<?php

class ControllerTest extends Component 
 {
 	function start() {
 		$this->read_input();
 	}
 	
 	function read_input() {
 		$this->call(new PromptDialog("Escribi algo: ", array('on_accept' => 'show_message')));
 	}
 	
 	function show_message($params) { 		
		$this->call(new NotificationDialog($params['text'], array('on_accept' => 'read_input'))); 		
 	}
 }
  
class ControllerApplication extends Application 
 {
 	function &set_root_component() {
 		return new ControllerTest;
 	}
 }
 
 
 
 
?>