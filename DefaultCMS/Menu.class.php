<?php

class Menu extends Component
{
	var $rendered = '';
	function declare_actions() {
		return array('menuclick');
	}
	function initialize(){
		$this->add_component(new Text($_SESSION[sitename]["Username"]), "username");
		$this->add_component(new Text($s = sitename), "SiteName");
		$this->add_component(new FormComponent, 'menus');
		$this->menus();
	}
	function newmenu (){
		$ms =& $this->menus;
		$cs =& $ms->__children;
		$ks = array_keys($cs);
		foreach($ks as $k){
			$ms->$k->delete();
		}
		$this->menus();
	}
	function menus (){
			$log = array('Component'=>'Logout');
			$this->additem($log,'Logout');
			$menus =& MenuSection::availableMenus();
			$ks =& array_keys($menus);
			foreach ($ks as $k) {
				$menu =& $menus[$k];
			    $col =& $menu->itemsVisible();
			    $ks2 =& array_keys($col);
			    foreach($ks2 as $k2){
			    	$menu =& $col[$k2];
					$this->additem(
						array_merge(
							array('Component'=>$menu->controller->value),
							parse_str($menu->params->value)
							),
						$menu->name->value);

		    	}
			}
			$arr = get_subclasses("PersistentObject");
			foreach ($arr as $name){
				if (fHasPermission($_SESSION[sitename]["id"], array("*","$name=>Menu"))){
					$this->addelement($name, $name);
				}
			}
	}
	function addelement($obj, $text) {
		$comp = array('Component'=>'ShowCollectionComponent','ObjType'=>$obj);
  		return $this->additem($comp, $text);
	}
	function additem(&$comp, $text){
		$this->menus->add_component(new MenuItemComponent($this, $text, $comp));
	}
	function menuclick(&$comp){
		$c =& new $comp['Component']($comp);
		$this->triggerEvent('menuClicked', $c);
	}

}

class MenuItemComponent extends Component{
	var $menu, $text, $item;
	function MenuItemComponent (&$menu, $text, &$item){
		$this->menu =& $menu;
		$this->text = $text;
		$this->item =& $item;
		parent::Component();
	}
	function declare_actions(){}
	function initialize(){
		$this->add_component(new ActionLink($this->menu, 'menuclick', $this->text, $this->item), "link");
	}
}

?>