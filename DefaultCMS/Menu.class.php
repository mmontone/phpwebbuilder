<?php
class Menu extends Component {
	var $rendered = '';
	function declare_actions() {
		return array (
			'menuclick'
		);
	}
	function initialize() {
		$this->add_component(new Text(new ValueHolder($_SESSION[sitename]["Username"])), "username");
		$this->add_component(new Text(new ValueHolder($s = sitename)), "SiteName");
		$this->add_component(new FormComponent($vm = null), 'menus');
		$this->menus();
	}
	function newmenu() {
		$ms = & $this->menus;
		$cs = & $ms->__children;
		$ks = array_keys($cs);
		foreach ($ks as $k) {
			$ms-> $k->delete();
		}
		$this->menus();
	}
	function menus() {
		$this->realMenus();
		$this->objMenus();
	}
	function realMenus() {
		$menus = & MenuSection :: availableMenus();
		$ks = & array_keys($menus);
		foreach ($ks as $k) {
			$this->realMenuSection($menus[$k]);
		}
	}
	function realMenuSection(& $menu) {
		$sect = & new MenuSectionComponent();
		$this->add_component($sect);
		$sect->add_component(new Text(new ValueHolder($menu->name->value)), 'secName');
		$col = & $menu->itemsVisible();
		$ks2 = & array_keys($col);
		foreach ($ks2 as $k2) {
			$menu = & $col[$k2];
			$this->additem(array_merge(array (
				'Component' => $menu->controller->value
			), parse_str($menu->params->value)), $menu->name->value, $sect);
		}
	}
	function objMenus() {
		$arr = get_subclasses("PersistentObject");
		$sect = & new MenuSectionComponent();
		$this->add_component($sect);
		$sect->add_component(new Text(new ValueHolder($t = 'Objects')), 'secName');
		foreach ($arr as $name) {
			if (fHasPermission($_SESSION[sitename]["id"], array (
					"*",
					"$name=>Menu"
				))) {
				$this->addelement($name, $name, $sect);
			}
		}
		$log = array (
			'Component' => 'Logout'
		);
		$this->additem($log, 'Logout', $sect);
	}
	function addelement($obj, $text, & $sect) {
		$comp = array (
			'Component' => 'ShowCollectionComponent',
			'ObjType' => $obj
		);
		return $this->additem($comp, $text, $sect);
	}
	function additem(& $comp, $text, & $sect) {
		$sect->add_component(new MenuItemComponent($this, $text, $comp));
		//echo "<br/>used memory for $text:" .memory_get_usage();
	}
	function menuclick(& $comp) {
		$c = & new $comp['Component'] ($comp);
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
		$this->add_component(new ActionLink($this->menu, 'menuclick', $this->text, $this->item), "link");
	}
}
?>