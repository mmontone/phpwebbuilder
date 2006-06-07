<?
/**
 * Saves an object from a form
 */
 
require_once dirname(__FILE__)."/../Controller.class.php"; 


class SaveController extends Controller {
	function permissionNeeded($form){
		$act = $form["Action"];
		if ($act=="Update" || $act=="Insert" || $act=="Delete"){
			if ($act=="Update") $act="Edit";
			if ($act=="Insert") $act="Add";
			$obj = $form["ObjType"];
			return array("*", 
					"*=>".$act, 
					$obj."=>*", 
					$obj."=>".$act);
		} else {
			return array("No_Can_Do");
		}
	}
	function &begin ($form) {
		trace("Saving object");
		// Create an empty object of the type specified in the form
		$obj =& new $form["ObjType"];

		// Recover the object the form represents
		$loader =& new Loader;
		$loader =& $loader->loadFor($obj);
		$valid = $loader->readForm($form, &$error_msgs);
		trace("Object after reading:".print_r($obj, TRUE));

		$newaddress = $loader->newaddress;
		$bs = $valid?"valid":"invalid";
		trace("Where to now?".$newaddress . $bs);
		if ($valid) {
			if ($newaddress == "") {
				$sc =& new ShowController;
				/*TODO:
				 * Eliminate the lack of newaddress.
				 */
				trace("Now showing the collection for ".print_r($form, TRUE));
				$form["Action"] = "List";
				return $sc->execute("begin", $form);
			} else {
				trace("Going to ". $newaddress);
				header ("location:". $newaddress);
			}
		} else {
                        $sc =& new ShowController;
			/*TODO:
			 * Eliminate the lack of newaddress.
			 */
			trace("Now showing the editing for ".print_r($form, TRUE));
			if ($form["Action"] =="Update") $form["Action"] ="Edit";
			if ($form["Action"] =="Insert") $form["Action"] ="Add";
			$sc->setErrors($error_msgs);
			return $sc->execute("begin", $form);
		}
		
	}
}
?>