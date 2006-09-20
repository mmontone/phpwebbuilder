<?php
class Menu extends Component {
	function initialize() {
		$this->addComponent(new Text(new ValueHolder($s = sitename)), "SiteName");
		$this->addComponent(new CompositeWidget, 'menus');
		$this->addComponent(new ActionLink($this, 'newmenu', 'refresh', $n=null), 'refresh');
		$this->newmenu();
	}
	function newmenu() {
		$u =& User::logged();
		$un = $u->user->getValue();
		$this->addComponent(new Text(new ValueHolder($un)), "username");
		$this->menus->deleteChildren();
		$this->menus();
	}
	function menus() {
		$this->realMenus();
		$this->objMenus();
	}
	function realMenus() {
		$ms = & new PersistentCollection('MenuSection');
		$self =& $this;
		$ms->map($f=lambda('&$m','$self->realMenuSection($m);',get_defined_vars()));
	}
	function realMenuSection(& $menu) {
		$sect = & new MenuSectionComponent();
		$mv = $menu->name->getValue();
		$sect->addComponent(new Text(new ValueHolder($mv)), 'secName');
		$col = & $menu->items->collection->elements();
		$ks2 =  array_keys($col);
		$arr=array();
		foreach ($ks2 as $k2) {
			$menu = & $col[$k2];
			$this->additem($arr[$k2]=array ('bookmark'=>'MenuItem',
				'Component' => $menu->controller->getValue()
			, 'params'=>$menu->params->getValue()), $menu->name->getValue(), $sect);
		}
		$this->menus->addComponent($sect);
	}
	function objMenus() {
		$arr = get_subclasses('PersistentObject');
		//$arr = Application::GetPersistentClasses();
		$sect = & new MenuSectionComponent();
		$sect->addComponent(new Label('Objects'), 'secName');
		$u =& User::logged();
		$temp = array();
		foreach ($arr as $p=>$c) {
			$class =& $arr[$p];
			$obj =& new $class;
			$sect->addComponent(new MenuItemComponent($this, $obj->displayString,
					$temp[$p] = array ('bookmark'=>'CollectionViewer',
					'class' => $class)));
		}
		$log = array ('bookmark'=>'MenuItem',
			'Component' => 'Logout'
		);
		$this->additem($log, 'Logout', $sect);
		$this->menus->addComponent($sect);
	}
	function additem(& $comp, $text, & $sect) {
		$sect->addItem(new MenuItemComponent($this, $text, $comp));
	}
	function menuclick(& $comp) {
		$c = & new $comp['Component'] ($comp['params']);
		$this->triggerEvent('menuClicked', $c);
	}
}

class MenuSectionComponent extends Component {
	var $add = false;
	/*function checkAddingPermissions(){
		return $this->add;
	}*/
	function addItem(&$i){
		$this->add = $this->addComponent($i);
	}
}

class MenuItemComponent extends Component {
	var $text, $items;
	function MenuItemComponent(& $menu, $text, & $items) {
		$this->text = $text;
		$this->items =& $items;
		parent :: Component();
	}
	/*function chechAddingPermissions(){
		return ($this->componentAt('link')!=null);
	}*/
	function initialize(){
		$bk =$this->items['bookmark'];
		unset($this->items['bookmark']);
		$this->addComponent(new NavigationLink($bk, $this->text,$this->items), "link");
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
		parse_str($params['params'], $form);
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