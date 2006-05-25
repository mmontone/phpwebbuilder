<?

//require_once dirname(__FILE__) . '/Controller.class.php';

/**
 * Shows the specified View, for the specified object.
 */
class RenderController extends Controller {
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
		$rc = new SaveController;
		return $rc->execute($form);
	}
	function begin ($form) {
		if ($form["Action"]=="List") {
			return $this->showColec($form);
		} else {
			return $this->showObj($form);
		}
	}
	function showObj ($form) {
		if (isset($form["ViewType"])) {
			$view = new $form["ViewType"];
		} else {
			$form["ViewType"] = "HtmlTable";
			$view = new HtmlTable;
		}
		if (!isset($_REQUEST["showHTML"])) {$showHTML=TRUE;} else $showHTML=$_REQUEST["showHTML"]=="TRUE";
		if (!isset($_REQUEST["Linker"])) {$_REQUEST["Linker"]="MixLinker";}
			// Create an empty object of the type specified in the form
			$obj = new $form["ObjType"];
			/*TODO
			 * Find a way to Read an object from a Form, without using the
			 * ReadView.
			 */
			$load = new Loader;
			$load = $load->loadFor(&$obj);
			$load->readForm($form, &$error_msgs);
			$obj = $load->obj;
			$html = $view->viewFor($obj);

			$linker = new $_REQUEST["Linker"]();
			if ($showHTML) {$text .= $html->headers($form);}
			if ($form["showXMLHeader"]=="TRUE") {
				header('Content-Type: application/xml');
				$text .=  "<?xml version=\"1.0\"?>";
			}
			$text .= $html->show($linker);
			if ($form["showXMLHeader"]=="TRUE") {
				$text = ereg_replace("&","&#39;",$text);
			}
			if ($showHTML)$text .=  $html->footers();
			return $text;
		}
	function showColec ($form) {
		if (isset($form["ViewType"])) {
			$view = new $form["ViewType"];
		} else {
			$form["ViewType"] = "HtmlTable";
			$view = new HtmlTable;

		}
		if (!isset($_REQUEST["showHTML"])) {$showHTML=TRUE;} else $showHTML=$_REQUEST["showHTML"]=="TRUE";
		if (!isset($_REQUEST["Linker"])) {$_REQUEST["Linker"]=MixLinker;}
			// Create an empty object of the type specified in the form
			$obj = new PersistentCollection($form["ObjType"]);
			/*TODO
			 * Find a way to Read an object from a Form, without using the
			 * ReadView.
			 */
			$load = new Loader;
			$load = $load->loadFor(&$obj);
			$load->readForm($form, &$error_msgs);
			$obj = $load->obj;

			$render = new Renderer($view, new $form["Action"], new $_REQUEST["Linker"]() );

			$render->render($obj);

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
