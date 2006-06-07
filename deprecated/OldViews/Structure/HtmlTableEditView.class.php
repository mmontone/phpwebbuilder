<?

require_once(dirname(__FILE__) . "/../Action/HtmlTableEditFieldView.class.php");
require_once("HtmlFormEditView.class.php");

class HtmlTableEditView extends HtmlFormEditView {
	function fieldShowObjectFactory () {
		return new HtmlTableEditFieldView;
	}
	function visitedPersistentCollection ($obj) {
		$view = new PersistentCollectionHtmlTableEditView;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject ($obj) {
		$view = new PersistentObjectHtmlTableEditView;
		$view->obj = $obj;
		return $view;
	}

    function visitedFile(&$file) {
        trigger_error('Vistando la vista del file');
        $f = 'FileHtmlTableEditView';
        $view = new $f;
        $view->obj = $file;
        return $view;
    }
}

class PersistentObjectHtmlTableEditView extends HtmlTableEditView  {
	function showFields($linker, $fields, $hidden=array()) {
		$ret="<div id=\"container\">";
		$ret .= $this->formHeader($hidden);
        $ret .= $this->formAppend();
        $ret .= "\n<table><tbody>";
        $ret .= $this->fieldsForm($linker, $fields, TRUE);
        $ret .= "\n</tbody></table>";
		$ret .= $this->formFooter();
		$ret .= $linker->linkSubmit($this->formId());
        $ret .= $linker->linkCancel($this->formId(), get_class($this->obj));
		$ret.="</div>";
		return $ret;
	}
	function showErrors(&$error_msgs){
		$valid = count($error_msgs)==0;
		if (!$valid) {
		   $view= &$this;
           $ret = "<div class=\"error_msgs\">";
		   foreach ($error_msgs as $error_msg) {
				$ret .= "<div>";
				$ret .= "<img title=\"Edit\" src=\"". icons_url . "stock_cancel.png\"/>";
				$ret .= $error_msg;
				$ret .= "</div>";
			}
			$ret .= "</div>";
			foreach ($error_msgs as $field=>$error_msg) {
				$view->declare_invalid_field($field);
			}
		}
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
	function listForm ($linker) {
		$ret = "\n<tr>";
		foreach($this->obj->allFields() as $index=>$field){
			$showField = $this->fieldShowObject($field);
            $ret .= $showField->listForm($this->obj);
       	}
		// Edit
		if (fHasAnyPermission($_SESSION[sitename]["id"], get_class($this->obj),"Edit")) {
				$ret .= "\n<td class=\"operation\">" .
					$linker->showObjEdit($this->obj->formphp, get_class($this->obj), $this->obj->getId()) .
					"</td>";
		}
		// Delete
		if (fHasAnyPermission($_SESSION[sitename]["id"], get_class($this->obj),"Delete")) {
			$ret .= "\n<td class=\"operation\">" .
					$linker->showObjDelete($this->obj->formphp, get_class($this->obj), $this->obj->getID(), $this->obj->indexValues()).
					"</td>";
		}
		$ret .= "\n</tr>";
		return $ret;
	}

}

class PersistentCollectionHtmlTableEditView extends HtmlTableEditView  {
      function show ($linker) {
	 	 $ret .= $this->showElements($linker);
	 	 $ret.=$this->showLinks($linker);
	 	 return $ret;
      }
      function showElements($linker) {
         $colec = $this->obj;

	 	 $ret = "\n<table><tbody>";

         $obj = new $colec->dataType;
		 $html = $this->viewFor($obj);
         $ret .= $html->listHeader($linker, $this);
         $objs = $colec->objects();
         if ($objs) {
         	foreach($objs as $index=>$object) {
	 	 		$html = $this->viewFor($object);
		 		$ret .= $html->listForm($linker);
         	}
         }
         $ret .= "\n</tbody></table>";
         return $ret;
      }

      function showObjects(&$linker) {
      	return $this->showElements($linker);
      }

      function showLinks($linker) {
      	$colec = $this->obj;
		 if (fHasAnyPermission($_SESSION[sitename]["id"], $this->obj->dataType,"Add")) {
		    $ret .= $linker->colecAddLink($colec->dataType, $colec->formphp);
		 }
		  if (($this->obj->offset-$this->obj->limit) < 0) {
		  	$back_offset=0;
		  } else {
		  	$back_offset = $this->obj->offset-$this->obj->limit;
		  }
		  $ret .= $linker->showColecBack($this, array("offset"=>$back_offset));
		  $ret .= $linker->showColecNext($this, array("offset"=>($this->obj->offset+$this->obj->limit)));
		  //$ret .= $linker->showObjSearch($this, $this->obj->dataType);

         return $ret;
      }
      function showLinksField($object, $field, $linker) {
      	$colec = $this->obj;
		 if (fHasAnyPermission($_SESSION[sitename]["id"], $this->obj->dataType,"Add")) {
		    $ret .= $linker->showObjAdd($colec->formphp, $colec->dataType, $colec->dataType.$field->field->fieldname ."=". $field->field->value);
		 }
		  if (($this->obj->offset-$this->obj->limit) < 0) {
		  	$back_offset=0;
		  } else {
		  	$back_offset = $this->obj->offset-$this->obj->limit;
		  }
		  $ret .= $linker->showColecBack($this, array("offset"=>$back_offset));
		  $ret .= $linker->showColecNext($this, array("offset"=>($this->obj->offset+$this->obj->limit)));
		  $ret .= $linker->showObjSearch($this, $this->obj->dataType);

         return $ret;
      }
      function asSelect ($linker, $name, $default, $void) {
        $ret .= "\n<select name=\"$name\">";
        if ($void!=""){
        	$ret .= "<option value=\"0\">$void</option>";
        }
        $this->obj->limit=5000;
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlTableEditView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "</select>";
        $obj = new $this->obj->dataType;
        $ret .= $linker->showSelectAdd($obj->formphp, get_class($obj), $name);
        return  $ret;
      }
    function userSelect ($linker, $name, $default, $void) {
		$obj = new $this->obj->dataType;
		$datatype = get_class($obj);
		if($_SESSION[$datatype."ID"]) {
			$ret = $_SESSION[$datatype."NyA"];}
		else{
			$formphp = "Action.php";
			$ret = "<input type=\"button\" name=\"nuevo\" value=\"Nuevo Usuario\" onclick=\"javascript:showSelectAdd ('$formphp', '$datatype', this)\"/>";
	        	$href="'Action.php?Controller=InsertedLoginController&userType=$datatype'";
			$script = "goAjax($href, callbackInsert, this)";
			$ret .= "<input type=\"button\" name=\"login\" value=\"Usuario existente\" onclick=\"javascript:$script\"/>";
		}
        return  $ret;
    }
      function voidOption () {}
	function makeLinkAddress ($action, $append) {
		$link = new PlainLinker;
		return $link->makelinkAddressPersistentCollection($this, $action, $append);
	}

}

require_once dirname(__FILE__) . '/FileHtmlTableEditView.class.php';

?>