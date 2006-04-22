<?

require_once dirname(__FILE__)."/../ViewStructure.class.php";

class HtmlTable extends ViewStructure {
	function visitedPersistentCollection (&$obj) {
		$view = new HTMLTablePersistentCollection;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject (&$obj) {
		$view = new HtmlTablePersistentObject;
		$view->obj = $obj;
		return $view;
	}
    function visitedFile(&$file) {
        trigger_error('Vistando la vista del file');
        $view = new FileHtmlTableEditView;
        $view->obj = $file;
        return $view;
    }
}
?>