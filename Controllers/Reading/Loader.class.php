<?

require_once dirname(__FILE__) . '/fields/HtmlReadFieldView.class.php';
require_once dirname(__FILE__) . '/PersistentObjectLoader.class.php';
require_once dirname(__FILE__) . '/../../OldViews/Structure/AbstractView.class.php';

class Loader extends AbstractView {
    var $newaddress = "";
   	function &loadFor(&$obj){
    		return $this->viewFor($obj);
    }
    function &viewFor(&$obj){
    		return $obj->visit($this);
    }
    function &fieldShowObjectFactory () {
        return new HtmlReadFieldView;
    }
    function &visitedPersistentCollection (&$obj) {
        $view =& new PersistentCollectionLoader;
        $view->obj =& $obj;
        return $view;
    }
    function &visitedPersistentObject (&$obj) {
        $view =& new PersistentObjectLoader;
        $view->obj =& $obj;
        return $view;
    }

    function &visitedFile(&$file) {
        $view =& new FileLoader;
        $view->obj =& $file;
        return $view;
    }

}

?>