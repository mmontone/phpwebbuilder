<?php
class Menu extends Component {
	function initialize() {
		$this->addComponent(new Text(new ValueHolder($_SESSION[sitename]["Username"])), "username");
		$this->addComponent(new Text(new ValueHolder($s = sitename)), "SiteName");
		$this->addComponent(new CompositeWidget, 'menus');
		$this->menus();
	}
	function newmenu() {
		$this->addComponent(new Text(new ValueHolder($_SESSION[sitename]["Username"])), "username");
		$this->menus->deleteChildren();
		$this->menus();
	}
	function menus() {
		$this->realMenus();
		$this->objMenus();
	}
	function realMenus() {
		$menus = & MenuSection :: availableMenus();
		$ks = array_keys($menus);
		foreach ($ks as $k) {
			$this->realMenuSection($menus[$k]);
		}
	}
	function realMenuSection(& $menu) {
		$sect = & new MenuSectionComponent();
		$this->menus->addComponent($sect);
		$mv = $menu->name->getValue();
		$sect->addComponent(new Text(new ValueHolder($mv)), 'secName');
		$col = & $menu->itemsVisible();
		$ks2 =  array_keys($col);
		$arr=array();
		foreach ($ks2 as $k2) {
			$menu = & $col[$k2];
			$this->additem($arr[$k2]=array (
				'Component' => $menu->controller->getValue()
			, 'params'=>$menu->params->getValue()), $menu->name->getValue(), $sect);
		}
	}
	function objMenus() {
		$arr = get_subclasses('PersistentObject');
		$sect = & new MenuSectionComponent();
		$this->menus->addComponent($sect);
		$sect->addComponent(new Text(new ValueHolder($t = 'Objects')), 'secName');
		$u =& User::logged();
		$temp = array();
		foreach ($arr as $p=>$c) {
			$class =& $arr[$p];
			$obj =& new $class;
			PermissionChecker::addComponent($sect,
				new MenuItemComponent($this, $obj->displayString,
					$temp[$p] = array ('Component' => 'CollectionViewer',
					'params' => new PersistentCollection($class))),
				new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Menu', '*')));
		}
		$log = array (
			'Component' => 'Logout'
		);
		$this->additem($log, 'Logout', $sect);
	}
	/*function addelement($class, $text, & $sect) {
		$comp = array (
			'Component' => 'CollectionViewer',
			'params' => new PersistentCollection($class)
		);
		return $this->additem($comp, $text, $sect);
	}*/
	function additem(& $comp, $text, & $sect) {
		$sect->addComponent(new MenuItemComponent($this, $text, $comp));
	}
	function menuclick(& $comp) {
		$c = & new $comp['Component'] ($comp['params']);
		$this->triggerEvent('menuClicked', $c);
	}

}

class MenuSectionComponent extends Component {}

class MenuItemComponent extends Component {
	var $menu, $text, $item;
	function MenuItemComponent(& $menu, $text, & $item) {
		$this->menu = & $menu;
		$this->text = $text;
		$this->item = & $item;
		parent :: Component();
	}
	function declare_actions() {}

	function initialize() {
		$this->addComponent(new ActionLink($this->menu, 'menuclick', $this->text, $this->item), "link");
	}
}
?>