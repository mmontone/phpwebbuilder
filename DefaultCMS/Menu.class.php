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
			$this->additem($arr[$k2]=array ('bookmark'=>'MenuItem',
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
			/*PermissionChecker::addComponent($sect,

				new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Menu', '*')));*/
			$sect->addComponent(new MenuItemComponent($this, $obj->displayString,
					$temp[$p] = array ('bookmark'=>'CollectionViewer',
					'class' => $class)));
		}
		$log = array ('bookmark'=>'MenuItem',
			'Component' => 'Logout'
		);
		$this->additem($log, 'Logout', $sect);
	}
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
		$items = $this->item;
		$bk =$items['bookmark'];

		unset($items['bookmark']);
		$this->addComponent(new NavigationLink($this->item['bookmark'], $this->text,$items), "link");
	}
}

class MenuItemBookmark extends Bookmark{
	function launchIn(&$app, $params){
		$con =& new $params['Component']($params['class']);
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