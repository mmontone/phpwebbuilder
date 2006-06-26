<?php

class NavigationLink extends CommandLink{
	function NavigationLink($bookmark, $name, $params=array()){
		parent::CommandLink(array('text'=>$name,
				'proceedFunction'=> new FunctionObject($this, 'navigate')));
		$this->bookmark = $bookmark;
		$this->params = $params;
	}
	function initialize(){}
	function initializeView(&$view){
		$view->setAttribute('href', toAjax($this->app->setLinkTarget($this->bookmark, $this->params)));
	}
	function navigate(){
		$app =& Application::instance();
		$app->navigate($this->bookmark, $this->params);
	}
}
?>