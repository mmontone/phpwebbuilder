<?php

class XMLPersistenceManager extends FilePersistenceManager{
	function save(&$object){
		$x =& XMLNameView($object);
		file_put_contents($this->fileNameForObject($object),$x->show());
	}
	function extension(){
		return '.xml';
	}
	function &load($class, $id){
		$fn = $this->fileNameFor($class, $id);
		$parser =& new XMLParser;
		$xml = $parser->parse($fn);
		trigger_error("Unfinished");
		print_backtrace();
		exit;
	}
}
?>