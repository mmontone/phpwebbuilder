<?

require_once("Controller.class.php");

/**
 * Controller for showing the result of a search on an object
 */

class SearchController extends Controller {
	function begin ($form) {
		$obj = new $form["ObjType"];
		$col = new PersistentCollection($form["ObjType"]);
		$view = new HTMLTableSearchView;
		foreach ($obj->allFields() as $i => $f) {
			//echo "campo: ".$i."<BR>";
			if ($form[filter."$i"]=="yes") {
			//	echo "se filtra por $i";
				$vf = $view->fieldShowObject($f); 
				$col->conditions[$i]=$vf->searchQuery($form, $view);
			}
		}
		$view = new HTMLTableEditView;
		$view = $view->viewFor($col);
		$dir = $view->makeLinkAddress("Show",array());
		/*foreach($_REQUEST as $name=>$value) {
  			echo $name ."=". $value."<br>";
		} 
		echo "dir=".$dir."<BR>";
		print_r($col);*/
		header("location:".$dir );
	} 
}

?>
