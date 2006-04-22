<?

class MenuItem extends PersistentObject {
    function initialize () {
         $this->table = "MenuItem";
         $this->addField(new textField("name", TRUE));
         $this->addField(new textField("controller", FALSE));
         $this->addField(new textField("params", FALSE));
         $this->addField(new indexField("section", TRUE, MenuSection));
    }
	function isVisible(){
		$conclass = $this->controller->value;
		$con =&	new $conclass;
		$form = array();
		parse_str($this->params->value, $form);
		return $con->hasPermission($form);
	}
    function showMenu (){
		if ($this->isVisible())
			return "<li><a href=\"Action.php?Controller=".$this->controller->value.$this->params->value."\">".$this->name->value."</a></li>";
		else 
			return "";
	}
}
?>
