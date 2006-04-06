<?php

class Menu extends Component
{
	var $rendered = "";
	function declare_actions() {
		return array('menuclick');
	}
	function initialize(){	}
	function newmenu (){
		$this->rendered="";
	}
    function render_on(&$html) {
    	
		$html->text("<div class=\"menu\" style=\"float:left;max-width:25%\">");
		$ret = '<blockquote>
		  <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Management</strong></font></p>
		</blockquote>
		  <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>User: '.$_SESSION[sitename]["Username"].'</strong></font></p>';
		  
		$ret .= $this->additem(array('Controller'=>'Logout'),'<img src="'.icons_url .'stock_exit.png" alt="Logout"/>');
		$ret .= $this->menus(); 
		$html->text($ret);
		$html->text("</div>");
	}
		
	function menus (){
		if ($this->rendered==""){
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
		return  $this->rendered;
	}
	function addelement($obj, $text) {
  		return $this->additem(array("Controller"=>"ShowController","ObjType"=>$obj,"Action"=>"List"), $text);
	}
	function additem($con, $text){
		return "<li><a href=\"".$this->render_action_link('menuclick',$con)."\">$text</a></li>";
	}
	function menuclick($params){
		$this->triggerEvent('menuClicked', $params);
	}

}

?>