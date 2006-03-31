<?

//require_once dirname(__FILE__). '/HtmlEditHtmlAreaView.class.php';
require_once dirname(__FILE__). '/HtmlEditFieldView.class.php';
//require_once dirname(__FILE__) . '/HtmlTableEditTextAreaView.class.php';
require_once dirname(__FILE__) . '/../../extra/FCKeditor/fckeditor.php';

class HtmlTableEditFieldView extends HtmlEditFieldView {
	function readForm ($object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name])) {
			$this->field->setValue($this->field->trim($form[$name]));
		}
	}
	function &viewFor(&$obj) {
		return $obj->visit($this);
	}
	function frmName($html) {
		return $html->formName() . $this->field->colName;
	}
	function listForm ($object) {
		if ($this->field->isIndex) {return $this->listObject($object);}
	}
	function visitedTextField($field) {
		$view = new HtmlTableEditTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmbedField($field) {
		$view = new HtmlTableEditEmbedFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEnumField($field) {
		$view = new HtmlTableEditEnumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateTimeField($field) {
		$view = new HtmlTableEditDateTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateField($field) {
		$view = new HtmlTableEditDateFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTimeField($field) {
		$view = new HtmlTableEditTimeFieldView;
		$view->field = $field;
		return $view;
	}

	function formObject ($object, $linker) {}
	function visitedCollectionField($field) {
		$view = new HtmlTableEditCollectionFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTextArea($field) {
		$view = new HtmlTableEditTextAreaView;
		$view->field = $field;
		return $view;
	}

        function visitedWikiArea($field) {
          $view = new HtmlTableEditTextAreaView;
          $view->field = $field;
          return $view;
	}

	function visitedBoolField($field) {
		$view = new HtmlTableEditBoolFieldView	;
		$view->field = $field;
		return $view;
	}
	function visitedNumField($field) {
		$view = new HtmlTableEditNumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedPasswordField($field) {
		$view = new HtmlTableEditPasswordFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmailField($field) {
		$view = new HtmlTableEditEmailFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIdField($field) {
		$view = new HtmlTableEditIdFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedSuperField($field) {
		$view = new HtmlTableEditSuperFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIndexField($field) {
		$view = new HtmlTableEditIndexFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedUserField($field) {
		$view = new HtmlTableEditUserFieldView;
		$view->field = $field;
		return $view;
	}
	function listObject ($object) {
		$html = new HtmlTableEditView;
		$html = $html->viewFor($object);
		return "\n<td>".$this->field->viewValue()."</td>";

	}
    function visitedBlobField(&$field) {
    $view = new HtmlTableEditBlobFieldView;
        $view->field = $field;
        return $view;
    }

    function visitedFilenameField(&$field) {
        $view = new HtmlTableEditFilenameFieldView;
        $view->field = $field;
        return $view;
    }

    function &visitedHtmlArea($field) {
        $view =& new HtmlTableEditHtmlAreaView;
        $view->field =& $field;
        return $view;
    }
}

class HtmlTableEditBlobFieldView extends HtmlTableEditFieldView {}

class HtmlTableEditCollectionFieldView extends HtmlTableEditFieldView {
	function formObject ($object, $linker) {
		$html = new HtmlTableEditView;
		$colec = $this->field->collection;
		$html = $html->viewFor($colec);
		$ret = $html->showElements($linker);
		$ret .= $html->showLinksField($object, $this, $linker);
		//$ret .= $linker->showObjAdd($colec->formphp, $colec->dataType, $colec->dataType.$this->field->fieldname ."=". $this->field->value);
		return $ret;
	}
	function show ($object, $objFields, $field_name) {
		if ($objFields) {return parent::show ($object, $objFields, $field_name);}
	}
}

class HtmlTableEditIndexFieldView extends HtmlTableEditFieldView {
	function formObject ($object, $linker) {
		$html = new HtmlTableEditView;
		$html = $html->viewFor($this->field->collection);
		return $html->asSelect($linker, $this->frmName($object), $this->field->getValue(), $this->field->nullValue);
	}
	function show ($object, $objFields, $field_name) {
		if ($objFields) {return parent::show ($object, $objFields, $field_name);}
	}
	function listObject(){
		if ($this->field->getValue()==0)
			return "<td>".$this->field->nullValue."</td>";
		else
			return "<td>".$this->field->viewValue()."</td>";
	}
}
class HtmlTableEditUserFieldView extends HtmlTableEditIndexFieldView {
	function formObject ($object, $linker) {
		$html = new HtmlTableEditView;
		$html = $html->viewFor($this->field->collection);
		return $html->userSelect($linker, $this->frmName($object), $this->field->getValue(), $this->field->nullValue);

	}

}

class HtmlTableEditEnumFieldView extends HtmlTableEditFieldView {
	function formObject ($object) {
		$ret = "\n<select name=\"".$this->frmName($object)."\">";
		foreach ($this->field->vals as $val) {
			$ret .= "<option value=\"".$val."\"";
				if ($this->field->value == $val) {$ret .= " selected=\"selected\" ";}
				$ret .= ">";
				$ret .= $val;
				$ret .= "\n                     </option>";
		}
        $ret .= "&nbsp;</select>";
		return $ret;
	}
}

class HtmlTableEditEmbedFieldView extends HtmlTableEditFieldView {
	function formObject ($object) {
		$html = new HtmlTableEditView;
		$o = $this->field->obj();
		$html = $html->viewFor($o);
		$ret = "<input type=\"hidden\" name=\"".$this->frmName($object)."\" value\"". $this->field->value ."\" />";
		return $html->fieldsForm(TRUE);
	}
}

class HtmlTableEditTextAreaView extends HtmlTableEditFieldView {
    function listObject () {
        return "\n<td>". $this->field->getValue()."</td>";
    }
    function formObject ($object) {
        $ret = "\n<textarea name=\"";
        $ret .= $this->frmName($object);
        $ret .= "\" cols=\"50\" rows=\"15\">";
        $ret .= $this->field->convFromHTML($this->field->getValue());
        $ret .= "\n</textarea>";
        return $ret;
    }
}

class HtmlTableEditDateTimeFieldView extends HtmlTableEditFieldView {
	function dateFormObject($object){
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mday";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["mday"];
		$ret .= "\" /> / ";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mon";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["mon"];
		$ret .= "\" /> /";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "year";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["year"];
		$ret .= "\" />";
		return $ret;
	}
	function formObject($object){
		return $this->dateFormObject($object) . $this->timeFormObject($object);
	}
	function timeFormObject($object) {
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "hours";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["hours"];
		$ret .= "\" /> :";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "minutes";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["minutes"];
		$ret .= "\" /> :";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "seconds";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["seconds"];
		$ret .= "\" />";
		return $ret;
	}
}

class HtmlTableEditDateFieldView extends HtmlTableEditDateTimeFieldView {
	function formObject($object){
		return $this->dateFormObject($object);
	}
}
class HtmlTableEditTimeFieldView extends HtmlTableEditDateTimeFieldView {
	function formObject($object){
		return $this->timeFormObject($object);
	}
}


class HtmlTableEditNumFieldView extends HtmlTableEditFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}
}

class HtmlTableEditTextFieldView extends HtmlTableEditFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}
}

class HtmlTableEditSuperFieldView extends HtmlTableEditFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"hidden\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" /> ".$this->field->getValue();
		return $ret;
	}
}

class HtmlTableEditPasswordFieldView extends HtmlTableEditTextFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"password\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "1\" size=\"60\" value=\"";
		//$ret .= $this->field->getValue();
		$ret .= "\" />";
		$ret .= "\n<input type=\"password\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "2\" size=\"60\" value=\"";
		//$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}

}
class HtmlTableEditEmailFieldView extends HtmlTableEditTextFieldView {}

class HtmlTableEditIdFieldView extends HtmlTableEditFieldView {
	function formObject ($object) {
		return "\n<td align=\"center\">".$this->field->value."</td>";
	}
	function listObject () {
		return "\n<td>".$this->field->value."</td>";
	}
}


class HtmlTableEditBoolFieldView extends HtmlTableEditFieldView {
	function listObject () {
		if ($this->field->value) $text = 'yes';
        else $text = 'no';
        return "\n<td>$text</td>";
	}
	function formObject ($object) {
		if ($this->field->getValue()){ $chk  =" checked=\"checked\" ";} else { $chk  ="";}
		return "<input type=\"checkbox\" name=\"".$this->frmName($object)."\" $chk />";
	}
	function readForm ($object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name]) AND ($form[$name]=="on")) {$this->field->value=1; } else {$this->field->value=0; };
	}
}

class HtmlEditHtmlAreaView extends HtmlEditFieldView
{
    function HtmlEditHtmlAreaView() {
    }

    function formObject ($object) {
        $editor =& new FCKeditor($this->frmName($object));
        $editor->BasePath = site_url . 'admin/pwb/extra/FCKeditor/';
        $editor->Value = $this->field->getValue();
        return $editor->CreateHtml();
    }
}

class HtmlTableEditHtmlAreaView extends HtmlTableEditTextAreaView
{
    function HtmlTableEditHtmlAreaView() {}

    function formObject ($object) {
        $editor =& new FCKeditor($this->frmName($object));
        $editor->BasePath = site_url . 'admin/pwb/extra/FCKeditor/';
        $editor->Value = $this->field->getValue();
        $editor->Width = '700px';
        $editor->Height = '700px';
        return $editor->CreateHtml();
    }
}

?>
