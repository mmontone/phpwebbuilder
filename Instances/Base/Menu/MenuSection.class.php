<?
class MenuSection extends PersistentObject {
    function initialize () {
         $this->table = "MenuSection";
         $this->addField(new textField("name", TRUE));
         $this->addField(new numField("menuorder", TRUE));         
         $this->addField(new CollectionField("section", MenuItem));
    }
    function showMenu (){
	    $ret ="";
	    $col = $this->itemsVisible();
	    foreach($col as $menu){
	        $ret .= $menu->showMenu();
	    }
	    return "<h4>".$this->name->value."</h4><ul>" . $ret ."</ul>";
	}
	function itemsVisible(){
	    $col =& $this->MenuItemsection->collection;
	    $col->limit=0;
	    $menus0 =& $col->objects();
	    $menus = array();
		$menus =& array_filter($menus0, create_function('$elem', 'return $elem->isVisible();'));
		return $menus;
	}
	function isVisible(){
		return count($this->itemsVisible())>0;
	}
	function availableMenus(){
		$col = new PersistentCollection('MenuSection');
	    $col->limit=0;
		$col2 =& $col->objects();
		$menus = array();
		for($i=0; $i<count($col2); $i++){
			$elem =& $col2[$i];
		/*	if ($elem->isVisible()){
				$menus []=&$elem;
			}*/
		}
		return $menus;  
	}
}
?>