<?php

class Menu extends Component
{
	var $rendered = "";
	function declare_actions() {
		return array('menuclick');
	}
	function initialize(){
		$this->add_component(new Text('<p>Management</p>
		  <p>User: '.$_SESSION[sitename]["Username"].'</p>'));  
		$this->additem(array('Controller'=>'Logout'),'<img src="'.icons_url .'stock_exit.png" alt="Logout"/>');
		$this->menus(); 
	}
	function newmenu (){
		$this->__children=array();
		$this->initialize();
	}
	function menus (){
			$menus = MenuSection::availableMenus();
			$ret ="";
			foreach ($menus as $m) {
			    $ret .= "<h4>".$m->name->value."</h4><ul>";
			    $col = $m->itemsVisible();
			    foreach($col as $menu){
					$ret .= $this->additem(
						array_merge(
							array("Controller"=>$menu->controller->value),
							parse_str($menu->params->value)
							),
						$menu->name->value);
	
		    	}
		    	$ret .="</ul>";
			}
			$arr = get_subclasses("PersistentObject");
			$ret .= "<ul>";
			foreach ($arr as $name){
				if (fHasPermission($_SESSION[sitename]["id"], array("*","$name=>Menu")))
					$ret .= $this->addelement($name, $name); 
			}
			$ret .= "</ul>";
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