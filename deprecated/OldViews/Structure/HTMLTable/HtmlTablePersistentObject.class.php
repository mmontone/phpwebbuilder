<?

require_once "HtmlTable.class.php";

class HtmlTablePersistentObject extends HtmlTable {
	function dataHeader(){
		$ret="<div id=\"container\">";
		$ret .= $this->formHeader();
        $ret .= $this->formAppend();
        $ret .= "\n<table><tbody>";
		return $ret;
	}
	function showFields($linker, $fields) {
		$ret .= $this->dataHeader();
        $ret .= $this->fieldsForm($linker, $fields, TRUE);
        $ret .= $this->dataFooter();
        return $ret;   
	}
	function dataFooter(){
        $ret .= "\n</tbody></table>";
		$ret .= $this->formFooter();
		$ret .= $linker->linkSubmit($this->formId());
		$ret.="</div>";
		return $ret;
	}

	function tdinitField($field) {
		return "\n<tr><td width=\"16%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\"> ".$field->field->colName."</font></td>
		<td width=\"84%\"> <font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">";
	}
	function tdendField($field) {
		return "\n</font></td></tr>";
	}
	function listHeader ($linker, $colview) {
		$ret = "\n<tr>";
		foreach($this->obj->allFields() as $index=>$field){
			if ($field->isIndex) {
				  /*$view = $this->fieldShowObject($field);
				  $ret .= $view->listHeader($colview);*/
				 $ret .=$this->listHeaderField($linker, $field, $colview);
			}
		}
		$ret .= "<td></td>";
		return $ret . "\n</tr>";
	}
	function listHeaderField($linker, $field, $colview) {
               $ret = "\n<td align=\"center\">" .
               					$linker->showListHeader($colview, $field->colName) .
                               "</td>";
               return $ret;
	}
	function listElement ($renderer) {
		foreach($this->obj->allFields() as $index=>$field){
/*			$showField = $this->fieldShowObject($field);
			$ret .= $showField->listForm($this->obj);*/
			$ret .=  $renderer->showListField($field);
       	}
		// Edit
		if (fHasAnyPermission($_SESSION[sitename]["id"], get_class($this->obj),"Edit")) {
				$ret .= "\n<td class=\"operation\">" .
					$renderer->linker->showObjEdit($this->obj->formphp, get_class($this->obj), $this->obj->getId()) .
					"</td>";
		}
		// Delete
		if (fHasAnyPermission($_SESSION[sitename]["id"], get_class($this->obj),"Delete")) {
			$ret .= "\n<td class=\"operation\">" .
					$renderer->linker->showObjDelete($this->obj->formphp, get_class($this->obj), $this->obj->getID(), $this->obj->indexValues()).
					"</td>";
		}
		return $ret;
	}

}

?>