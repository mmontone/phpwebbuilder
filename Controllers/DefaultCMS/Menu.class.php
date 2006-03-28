<?php

class Menu extends Component
{
	function declare_actions() {
		return array('menuclick');
	}
    function render_on(&$html) {
    	
		$html->text("<div class=\"menu\" style=\"float:left;max-width:25%\">");
		$ret = '<blockquote>
		  <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Management</strong></font></p>
		</blockquote>
		  <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>User: '.$_SESSION[sitename]["Username"].'</strong></font></p>
		<a style="color:#FFFFFF" href="Action.php?Controller=Logout" target="_parent"><img src="'.icons_url .'stock_exit.png" alt="Logout"/></a>';
		$ret .= $this->menus(); 
		$html->text($ret);
		$html->text("</div>");
		
	}
		
	function menus (){ 
		$menus = MenuSection::availableMenus();
		$ret ="";
		foreach ($menus as $m) {
		    $ret .= "<h4>".$m->name->value."</h4><ul>";
		    $col = $m->itemsVisible();
		    foreach($col as $menu){
				$ret .= "<li><a href=\"".$this->render_action_link('menuclick',
					array_merge(
						array("Controller"=>$menu->controller->value),
						parse_str($menu->params->value)
						)
					)."\">".$menu->name->value."</a></li>\n";

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
		return $ret; 
	}
	function addelement($obj, $text) {
  		//return "<li><a href=\"Action.php?Controller=ShowController&ObjType=$obj&Action=List\">$text</a></li>";
  		return "<li><a href=\"".$this->render_action_link('menuclick',array("Controller"=>"ShowController","ObjType"=>$obj,"Action"=>"List"))."\">$text</a></li>";
	}
	function menuclick($params){
		$this->triggerEvent('menuChanged', $params);
	}

}

?>