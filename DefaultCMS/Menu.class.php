<?php
class InitialDefaultCMSComponent extends ContextualComponent {
	function initialize() {
		$this->addComponent($comp =& new Login);
		$comp->addInterestIn('logged', new FunctionObject($this, 'newmenu'));

	}
	function newmenu() {
		$u =& User::logged();
		$un = $u->user->getValue();
		$this->addComponent(new Text(new ValueHolder($un)), "username");
		$this->menus();
		$this->addAllMenus();
	}
	function menus() {
		$this->realMenus();
		$this->addNavigationMenu('Object Menus',new FunctionObject($this, 'objMenus'));
		//$this->objMenus();
	}
	function realMenus() {
		$ms = & new PersistentCollection('MenuSection');
		$self =& $this;
		$ms->map($f=lambda('&$m','$self->realMenuSection($m); return $x;',get_defined_vars()));
	}
	function realMenuSection(& $menu) {
		$col = & $menu->items->collection->elements();
		$ks2 =  array_keys($col);
		$arr=array();
		foreach ($ks2 as $k2) {
			$menu = & $col[$k2];
			$this->addNavigationMenu($menu->name->getValue(),new FunctionObject($this, 'callComponent', $menu));
		}
	}
	function callComponent($menu){
		$cont = $menu->controller->getValue();
		$this->call(new WrapperContextualComponent(new $cont($menu->params->getValue())));
	}
	function objMenus() {
		$arr = get_subclasses('PersistentObject');
		$temp = array();
		foreach ($arr as $p=>$c) {
			$class =& $arr[$p];
			$obj =& new $class;
			$this->addNavigationMenu($class,new FunctionObject($this, 'showClass', $class));
		}
	}
	function showClass($class){
		$this->call(new WrapperContextualComponent(new CollectionViewer(new PersistentCollection($class))));
	}
}

class MenuItemBookmark extends Bookmark{
	function launchIn(&$app, $params){
		$con =& new $params['Component']();
		$app->component->changeBody($this,$con);
	}
	function checkPermissions($params){
		$con =& new $params['Component'];
		$form = array();
		parse_str(@$params['params'], $form);
		return $con->hasPermission($form);
	}
}


class CollectionViewerBookmark extends Bookmark{
	function launchIn(&$app, $params){
		$con =& new CollectionViewer(new PersistentCollection($params['class']));
		$app->component->body->stopAndCall($con);
	}
	function checkPermissions($params){
		$u=& User::logged();
		return $u->hasPermissions(array($params['class'].'=>Menu', '*'));
	}
}
?>