<?php

class IniFilePersistenceManager extends FilePersistenceManager{
	function save(&$object){
		$ini = "lala :)";
		file_put_contents($this->fileNameForObject($object),$ini);
	}
	function extension(){
		return '.ini';
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