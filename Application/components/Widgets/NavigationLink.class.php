<?php

class NavigationLink extends CommandLink{
	function NavigationLink($bookmark, $name, $params=array()){
		parent::CommandLink(array('text'=>$name,
				'proceedFunction'=> new FunctionObject($this, 'navigate')));
		$this->bookmark = $bookmark;
		$this->params = $params;
	}
	function checkAddingPermissions(){
		$v = $this->bookmark.'Bookmark';
		$b =& new $v;
		return $b->checkPermissions($this->params);
	}
	function navigate(){
		$app =& Application::instance();
		$app->navigate($this->bookmark, $this->params);
	}
}
?>