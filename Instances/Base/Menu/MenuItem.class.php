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
		$conclass = $this->controller->getValue();
		$con =&	new $conclass;
		unset($conclass);
		$form = array();
		parse_str($this->params->getValue(), $form);
		return $con->hasPermission($form);
	}
}
?>
