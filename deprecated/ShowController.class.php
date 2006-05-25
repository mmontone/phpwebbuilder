<?

/**
 * Shows the specified View, for the specified object.
 */
class ShowController extends Controller {
	var $error_msgs = array();
	function permissionNeeded($form){
		trace("Starting...");
		$act = $form["Action"];
		if (strcasecmp($form["ObjType"],"PersistentCollection")==0) {
			$form["ObjType"] = $form["dataType"];
			$form["Action"]="List";
		};
		$obj = $form["ObjType"];
		if ($act == "Edit"||$act == "Show"||$act == "List"||$act == "Add")
			return array("*", 
					"*=>".$act, 
					$obj."=>*", 
					$obj."=>".$act);
		else return array("No_Can_Do");
	}
	function noPermission($form) {
		$rc =& new SaveController;
		return $rc->execute("begin", $form);
	}
	function begin($form) {
		if ($form["Action"]=="List") {
			return $this->showColec($form);
		} else {
			return $this->showObj($form);
		}
	}
	function setErrors(&$error_msgs){
		$this->error_msgs =& $error_msgs;
	}
	function showObj ($form) {
		if (isset($form["ViewType"])) {
			$view =& new $form["ViewType"];
		} else {
			$form["ViewType"] = "HtmlTableEditView";
			$view =& new HtmlTableEditView;
		} 
		if (!isset($_REQUEST["showHTML"])) {$showHTML=TRUE;} else $showHTML=$_REQUEST["showHTML"]=="TRUE";  
		if (!isset($_REQUEST["Linker"])) {$_REQUEST["Linker"]="MixLinker";}
			// Create an empty object of the type specified in the form
			$obj =& new $form["ObjType"];
			/*TODO
			 * Find a way to Read an object from a Form, without using the 
			 * ReadView.
			 */
			$load =& new Loader;
			$load =& $load->loadFor(&$obj);
			$load->readForm($form, &$error_msgs);
			$html =& $view->viewFor($obj);

			$linker =& new $_REQUEST["Linker"]();
			if ($showHTML) {$text .= $html->headers($form);}			
			if ($form["showXMLHeader"]=="TRUE") {
				header('Content-Type: application/xml');
				$text .=  "<?xml version=\"1.0\"?>";
			}
            $text .= $html->showErrors($this->error_msgs);
			$text .= $html->show($linker);
			if ($form["showXMLHeader"]=="TRUE") {
				$text = ereg_replace("&","&#39;",$text);		
			}
			if ($showHTML)$text .=  $html->footers();
			return $text;
		}
	function showColec ($form) {
		if (isset($form["ViewType"])) {
			$view =& new $form["ViewType"];
		} else {
			$form["ViewType"] = "HtmlTableEditView";
			$view =& new HtmlTableEditView;

		} 
		if (!isset($_REQUEST["showHTML"])) {$showHTML=TRUE;} else $showHTML=$_REQUEST["showHTML"]=="TRUE";  
		if (!isset($_REQUEST["Linker"])) {$_REQUEST["Linker"]=MixLinker;}
			// Create an empty object of the type specified in the form
			$obj =& new PersistentCollection($form["ObjType"]);
			/*TODO
			 * Find a way to Read an object from a Form, without using the 
			 * ReadView.
			 */
			$load =& new Loader;
			$load =& $load->loadFor(&$obj);
			$load->readForm($form, &$error_msgs);
			$html =& $view->viewFor($obj);

			$linker =& new $_REQUEST["Linker"]();
			if ($showHTML) {$text .= $html->headers($form);}
			if ($form["showXMLHeader"]=="TRUE") {
				header('Content-Type: application/xml');
				$text .= "<?xml version=\"1.0\"?>";
			}
			$text .=  $html->show($linker);
			if ($form["showXMLHeader"]=="TRUE") {
				$text = ereg_replace("&","&#39;",$text);		
			}
			if ($showHTML) $text .= $html->footers();
			return $text;
		}
}
?>