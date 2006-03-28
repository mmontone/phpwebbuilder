<?php
/**
 * This class Renders a View with the specified components
 * 
 * How does it work: It first 
 * 
 * 
 * */
class Renderer {
	/** 
	 * @var ViewStructure A Structure(Table, Div, XML),
	 */	
	var $structure;
	/**
	 * @var ViewAction An Action (Edit, Show, Search)
	 */
	var $action;
	/**
	 * @var ViewLinker A Linker (JScript, Plain) 
	 */
	var $linker;
	/**
	 * Creates a renderer
	 * @param ViewStructure $structure
	 * @param ViewAction $action
	 * @param ViewLinker $linker
	 */
    function Renderer($structure, $action, $linker) {
    	$this->structure = $structure;
    	$this->action = $action;
    	$this->linker = $linker;
    }
    function render($obj){
    	$this->persistentObject = $obj;    	
    	$text = $this->renderHeader($obj);
    	$text .= $this->renderBody($obj);
    	$text .= $this->renderFooter($obj);
    	return $text;
    }
    function renderHeader($obj){}
    function renderBody($obj){
    	$this->persistentObject = $obj;
    	$this->structure = $this->structure->viewFor($obj);
		$page = $this->structure->dataHeader($this);
		/* TODO:
		 * Complete Rendering
		 */
		$page .= $this->fields($obj);
		
		$page .= $this->structure->dataFooter($this);
		return $page;
    }
    function renderFooter($obj){}
    /**
     * Here we delegate the structure how to show the dataFields, 
     * using our help.
     */
    function fields($obj){
    	return $this->structure->dataFields($this);
    }
    /*
     * Collection: This methods are used to show a PersistenCollection.
     */
    
    function showListObject($object){
    	$page .= $this->structure->objectWrapperInit();
    	$s = $this->structure->viewFor($object);
    	$page .= $s->listElement($this);
    	$page .= $this->structure->objectWrapperEnd();
    	return $page;
    }
    function showListField($field){
    	$page .= $this->structure->fieldWrapperInit();    	
    	$page .= $field->viewValue();
    	$page .= $this->structure->fieldWrapperEnd();
    	return $page;    	    	
    }
    /*
     * Object: This methods are used to show a PersistenCollection.
     */
 
    function formName(){
	   	return $this->persistentObject->tableName(). $this->persistentObject->getID();
    }
}
?>