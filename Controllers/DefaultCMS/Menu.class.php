<?php

class Menu extends Component
{
	var $rendered = "";
	function declare_actions() {
		return array('menuclick');
	}
	function initialize(){
		$this->add_component(new Text('<p>Management</p><p>User: '.$_SESSION[sitename]["Username"].'</p>'));  
		$this->additem(array('Controller'=>'Logout'),'<img src="'.icons_url .'stock_exit.png" alt="Logout"/>');
		$this->menus(); 
	}
	function newmenu (){
		$ks = array_keys($this->__children);
		foreach ($ks as $k){
			$this->delete_component($k);
		}
		echo "items: ".count($this->__children);		
		$this->initialize();
		echo "items: ".count($this->__children);
		
	}
	function prepateToRender(){
		echo "preparing menu";
		parent::prepateToRender();
		echo "menu prepared";
	}
	function menus (){
			$menus = MenuSection::availableMenus();
			foreach ($menus as $m) {
			    $this->add_component(new Text("<h4>".$m->name->value."</h4><ul>"));
			    $col = $m->itemsVisible();
			    foreach($col as $menu){
					$ret .= $this->additem(
						array_merge(
							array("Controller"=>$menu->controller->value),
							parse_str($menu->params->value)
							),
						$menu->name->value);
	
		    	}
		    	$this->add_component(new Text("</ul>"));
			}
			$arr = get_subclasses("PersistentObject");
			$this->add_component(new Text("<ul>"));
			foreach ($arr as $name){
				if (fHasPermission($_SESSION[sitename]["id"], array("*","$name=>Menu")))
					$ret .= $this->addelement($name, $name); 
			}
			$this->add_component(new Text("</ul>"));
			$this->rendered = $ret;
	}
	function addelement($obj, $text) {
  		return $this->additem(array("Controller"=>"ShowController","ObjType"=>$obj,"Action"=>"List"), $text);
	}
	function additem($con, $text){
		$this->add_component(new Text('<li>'));
		$this->add_component(new ActionLink($this, 'menuclick', $text, $con));
		$this->add_component(new Text('</li>'));
	}
	function menuclick($params){
		$this->triggerEvent('menuClicked', $params);
	}

}

?>