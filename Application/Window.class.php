<?php

class Window extends ComponentHolder{
	var $ajaxCommands = array();
	var $urlManager;
	var $wholeView;
	function &getWindow(){
		return $this;
	}
	function setActiveInstance(&$window){
		global $active_window;
		$active_window[0] =& $window;
	}
	function &getActiveInstance(){
		global $active_window;
		return $active_window[0];
	}
	function & view() {
		return $this->wholeView;
	}
	function createView() {
			$this->urlManager =& new UrlManager($this);
			$this->wholeView = & new XMLNodeModificationsTracker;
			$this->wholeView->setAttribute('id', $this->getId());
			$tc =& new HTMLContainer('',array());
			$tc->setAttribute('class','Component');
			$app =& Application::instance();
			$app->page_renderer->initPage($this);
			$this->toFlush =& new ChildModificationsXMLNodeModification($this);
			$this->wholeView->parentNode =& $this;
			$this->wholeView->appendChild($tc);
			$this->wholeView->controller = & $this;
			$this->wholeView->getTemplatesAndContainers();
			$this->windowTitle =& new Label($this->parent->getTitle());
			$this->setWindowTitle();
			$this->component->linkToApp($this->parent);
			$this->component->view->toFlush->setTarget(new ReplaceChildXMLNodeModification($this->component, $this->component, $this->wholeView));
			$app->page_renderer->initialRender($this);
	}
	function addChildMod($pos,&$mod){
		$this->toFlush->addChildMod($pos,$mod);
	}
	function unsetChildWithId(){}
	function redraw() {
		$this->wholeView->replaceChild($this->wholeView->first_child(), clone($this->wholeView->first_child()));
	}

	function addAjaxCommand(&$cmd) {
		$this->ajaxCommands[] =& $cmd;
	}

	function &getAjaxCommands() {
		return $this->ajaxCommands;
	}
	function hasModifications(){
		return (count($this->toFlush->modifications) + count($this->ajaxCommands)) >0;
	}
	function render() {
		$myname = $this->owner_index();
		$ws =& $this->parent->windows;
		//echo $this->toFlush->printTree();
		foreach(array_keys($ws) as $win){
			if ($win!=$myname && $ws[$win]->hasModifications()){
				$modwins[]=$win;
			}
		}
		if ($modwins!=null)$this->addAjaxCommand(new AjaxCommand('refreshWindows', $modwins));
		echo $this->parent->page_renderer->render($this);
		$this->toFlush =& new ChildModificationsXMLNodeModification($this);
	}
	function getId() {
		return "app";
	}
	function getRealId() {
		return $this->parent->getRealId() . CHILD_SEPARATOR.$this->owner_index();
	}
	function setTitle($title){
		$this->windowTitle->setValue($title);
	}

	function setWindowTitle(){
		$this->wholeView->title=$this->windowTitle->getValue();
	}
	function navigate($bookmark, $params){
		$this->urlManager->navigate($bookmark, $params);
	}
	function goToUrl($url){
		$this->urlManager->goToUrl($url);
	}
	function resetUrl(){
		$this->urlManager->resetUrl();
	}
	function badUrl($bm, $params){
		$this->resetUrl();
	}
	function Window(&$component, $name){
		$app =& Application::Instance();
		parent::ComponentHolder(&$component, $name, &$app);
		$app->addWindow($this, $name);
	}
	function open($params=''){
		$w =& Window::getActiveInstance();
		$w->addAjaxCommand(new AjaxCommand('openWindow',array($this->owner_index(), $params)));
	}
	function close(){
		$this->addAjaxCommand(new AjaxCommand('window.close', array($this->owner_index())));
		unset($this->parent->windows[$this->owner_index()]); //Won't work, not rendered and removed.
	}
}
?>