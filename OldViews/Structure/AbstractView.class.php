<?
require_once("ViewStructure.class.php");
class AbstractView extends ViewStructure{
	function showErrors(&$error_msgs){}
	function &fieldShowObject (&$field) {
		$factory =& $this->fieldShowObjectFactory();
		return $factory->viewFor($field);
	}

}
?>
