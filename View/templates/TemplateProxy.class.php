<?php

class TemplateProxy {

    function TemplateProxy(&$template) {
    	$this->setTemplate($template);
    }
    function setTemplate(&$template){
    	$this->class = $template->getAttribute('class');
    	$this->handler = $template->getAttribute('handler');
    	global $template_objects;
    	$template_objects[$this->class] =&$template;
    	$file = $this->getFilename();
		$fo = fopen($file, 'w');
		fwrite($fo, serialize($template));
		fclose($fo);
    }
    function getFilename(){
    	$comp =& Compiler::Instance();
		return $comp->getTempDir('').strtolower(constant('app_class')).'-templates-'.$this->class.'.php';
    }
    function &getTemplate(){
    	global $template_objects;
    	if (!isset($template_objects[$this->class])){
    		$template_objects[$this->class] = unserialize(file_get_contents($this->getFilename()));
    	}
    	return $template_objects[$this->class];
    }
    function &instantiate(){
    	$t =& $this->getTemplate();
    	return $t->instantiate();
    }
    function isTemplateForClass(&$component){
    	return $component->hasType($this->class);
    }
	function getClass() {
		return strtolower($this->class);
	}
	function getHandler(){
		return $this->handler;
	}
}
?>