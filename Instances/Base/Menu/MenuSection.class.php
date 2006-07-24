<?

require_once pwbdir.'/Model/PersistentObject.class.php';

class MenuSection extends PersistentObject {
    function initialize () {
         $this->table = "MenuSection";
         $this->displayString = 'Menu section';
         $this->addField(new textField("name", TRUE));
         $this->addField(new numField("menuorder", array('is_index'=>true,'display'=>'Menu order')));
         $this->addField(new CollectionField(array('reverseField'=>'section', 'fieldName'=>'items','type'=>'MenuItem','display'=>'Sections')));
    }
    function showMenu (){
	    $col =& $this->itemsVisible();
	    ob_start();
	    foreach($col as $menu){
	        echo $menu->showMenu();
	    }
	    $ret = ob_get_contents();
	    return "<h4>" . $this->name->getValue() . "</h4><ul>" . $ret ."</ul>";
	}
	function &itemsVisible(){
		if (!isset($this->itemsVisible)){
		    $col =& $this->items->collection;
		    $col->limit=0;
		    $menus0 =& $col->objects();
		    unset($col);
		    $menus = array();
		    $ks = array_keys($menus0);
		    foreach($ks as $k){
		    	$elem =& $menus0[$k];
		    	if ($elem->isVisible()){
					$menus[]=& $elem;
		    	}
		    }
			$this->itemsVisible =& $menus;
		}
		return $this->itemsVisible;
	}
	function isVisible(){
		return count($this->itemsVisible())>0;
	}
	function &availableMenus(){
		$col =& new PersistentCollection('MenuSection');
	    $col->limit=0;
		$col2 =& $col->objects();
		unset($col);
		$menus = array();
		for($i=0; $i<count($col2); $i++){
			$elem =& $col2[$i];
			if ($elem->isVisible()){
				$menus []=&$elem;
			}
		}
		return $menus;
	}
}
?>