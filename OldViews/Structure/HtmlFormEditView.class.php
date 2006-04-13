<?

require_once "AbstractView.class.php";

class HtmlFormEditView extends AbstractView {
    function footers () {
			// Go back button
			$ret = "<a title=\"Go back\" href=\"javascript: history.go(-1)\"><img title=\"Go back\" src=\"". icons_url . "stock_undo.png\"/></a>";

			// Refresh button
			$ret .="<a href=\"javascript: location.reload()\"><img title=\"Refresh\" src=\"". icons_url . "stock_refresh.png\"/></a>";
			//$ret .= "\n</body>";
			//$ret .= "\n</html>";
			return $ret;
		}
	function selForm ($default) {
		$this->obj->load();
		$ret = "\n                     <option value=\"".$this->obj->getID()."\"";
		if ($this->obj->getID() == $default) {$ret .= " selected=\"selected\" ";}
		$ret .= ">";
		$ret .= $this->obj->indexValues();
		$ret .= "\n                     </option>";
		return $ret;
	}
	function showField ($field, $linker) {
		if ($field->field->colName == 'id') return "";
        $ret = $this->tdinitField($field);
		$ret .= $field->formObject($this, $linker);
        if (in_array($field->field->colName, $this->invalid_fields))
            $ret .= "<img title=\"Edit\" src=\"". icons_url . "stock_cancel.png\"/>";
		$ret .= $this->tdendField($field);
		return $ret;
	}
        function headers() {
			//$ret ="<html>\n";
			//$ret .="<head>\n";

			// The style
			if (isset($form["css"])) {
				$ret .="<link rel=\"stylesheet\" type=\"text/css\" href=\"".$form["css"]."\" />";
			} else {
				$ret .="<link rel=\"stylesheet\" type=\"text/css\" href=\"". site_url ."/css/standard.css\" />";
			}
			$ret .= "<link rel=\"shortcut icon\" href=\"favicon.ico\" >";
			// Scripts
			$ret .="
			<script>
			//The ask function. It asks before doing an action
			// Think this shoudnt be here
			function ask(action_str, object_str, href) {
				var res = confirm(\"Are you sure you want to \" + action_str + \" \" + object_str + \"?\");
				if (res) {
					window.location = href;
				}
			}
			</script>";




			// Close the header
			//$ret .= "</head>\n";
			//$ret .= "<body>\n";
			return $ret;

	}
	function formHeader($hiddenFields=array()) {
		$obj = $this->obj;
/*		$ret = "\n<form name=\"";
		$ret .= $this->formName();
		$ret .= "\" id=\"".$this->formId()."\"";
		$ret .= " method=\"post\" action=\"".$obj->formphp."\" enctype=\"multipart/form-data\">";*/
		$ret .= "\n   <input type=\"hidden\" name=\"ViewType\" value=\"". get_class($this) ."\" />";
		$ret .= "\n   <input type=\"hidden\" name=\"Controller\" value=\"ShowController\" />".		
				"\n   <input type=\"hidden\" name=\"ObjType\" value=\"". get_class($obj) ."\" />";
		foreach($hiddenFields as $name => $value)
			$ret.= "\n   <input type=\"hidden\" name=\"$name\" value=\"$value\" />";
/*		$ret .= "\n   <input type=\"hidden\" name=\"newAddress\" value=\"";
		$html = $this->viewFor(new PersistentCollection(get_class($obj)));
		$ret .= $html->makeLinkAddress("Edit", "") ."\" />";*/
		$ret .= "\n   <input type=\"hidden\" name=\"ObjID\" value=\"". $obj->getID() ."\" />";
		if ($obj->existsObject) {$act="Update";} else {$act="Insert";};
		$ret .= "\n   <input type=\"hidden\" name=\"Action\" value=\"$act\" />";
		return $ret;
	}
	function formFooter() {
        	//$ret = "\n</form>";
		return $ret;
	}
	function formName() {
		return $this->obj->tableName() . $this->obj->getID();
	}

	function formId() {

		return $this->obj->tableName() . "_" . $this->obj->getID();

	}

	function formAppend() {}
	function makelink($text, $action, $append) {
		$link = new PlainLinker;
		return $link->makelink($this, $text, $action, $append);
	}
}
?>
