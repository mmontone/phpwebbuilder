<?

require_once("AbstractFieldView.class.php");

class HtmlEditFieldView extends AbstractFieldView {
	function show($obj, $linker, $showObjFields) {
		/*if ($obj->is_invalid_field($this->field->colName)) {
			$ret =  "<img title=\"Edit\" src=\"". icons_url . "stock_cancel.png\"/>";
		} else $ret ="";*/
        $ret = "" ;

		return $obj->showField($this, $linker) . $ret;
	}
	function frmName($object) {
		return $object->formName() . $this->field->colName;
	}
	function visitedTextField($field) {
		$view = new HtmlEditTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmailField($field) {
		$view = new HtmlEditEmailFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateTimeField($field) {
		$view = new HtmlEditDateTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateField($field) {
		$view = new HtmlEditDateFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTimeField($field) {
		$view = new HtmlEditTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedCollectionField($field) {
		$view = new HtmlEditCollectionFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTextArea($field) {
		$view = new HtmlEditTextAreaView;
		$view->field = $field;
		return $view;
	}
	function visitedBoolField($field) {
		$view = new HtmlEditBoolFieldView	;
		$view->field = $field;
		return $view;
	}
	function visitedNumField($field) {
		$view = new HtmlEditNumFieldView;
		$view->field = $field;
		return $view;
	}
	function &visitedIdField(&$field) {
		$view =& new HtmlEditIdFieldView;
		$view->field =& $field;
		return $view;
	}
	function visitedSuperField($field) {
		$view = new HtmlEditIdFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIndexField($field) {
		$view = new HtmlEditIndexFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedUserField($field) {
		$view = new HtmlEditUserFieldView;
		$view->field = $field;
		return $view;
	}

    function &visitedFilenameField(&$field) {
        $view = new HtmlEditFilenameFieldView;
        $view->field =& $field;
        return $view;
    }

    function &visitedHtmlArea(&$field) {
        $view = new HtmlEditHtmlAreaView;
        $view->field =& $field;
        return $view;
    }
}


class HtmlEditCollectionFieldView extends HtmlEditFieldView {
	function formObject ($object) {
		$colec = $this->field->collection;
		$html = $object->viewFor($colec);
		$ret = $html->showElements();
		$htmlobj = $object->viewFor(new $colec->dataType);
		$ret .= $htmlobj->makelink("Agregar", "Add", "&".$colec->dataType.$this->field->fieldname."=".$object->obj->getID());
		return $ret;
	}
	function show ($object, $objFields) {
		if ($objFields) {return parent::show ($object, $objFields);}
	}
}

class HtmlEditIndexFieldView extends HtmlEditFieldView {
	function formObject ($object) {
		$html = $object->viewFor($this->field->collection);
		return $html->asSelect($this->frmName($object), $this->field->getValue());
	}
	function show ($object, $objFields) {
		if ($objFields) {return parent::show ($object, $objFields);}
	}
}
class HtmlEditUserFieldView extends HtmlEditIndexFieldView {
	function formObject ($object) {
		$html = $object->viewFor($this->field->collection);
		return $html->userSelect($this->frmName($object), $this->field->getValue());
	}
}

class HtmlEditNumFieldView extends HtmlEditFieldView {
	function formObject($object) {
		$ret .= "\n                     <input type=\"text\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\">";
		return $ret;
	}
}

class HtmlEditTextFieldView extends HtmlEditFieldView {
	function formObject($object) {
		$ret = "\n                     <input type=\"text\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\">";
		return $ret;
	}
}

class HtmlEditEmailFieldView extends HtmlEditTextFieldView {}

class HtmlEditIdFieldView extends HtmlEditFieldView {
	function formObject ($object) {
		return "\n                     <p>&nbsp;".$this->field->value . "</p>";
	}
}

class HtmlEditTextAreaView extends HtmlEditFieldView {
	function formObject ($object) {
		$ret .= "\n                     <textarea name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" >";
		$ret .= $this->field->convFromHTML($this->field->getValue());
		$ret .= "\n                     </textarea>";
		return $ret;
	}
}

class HtmlEditBoolFieldView extends HtmlEditFieldView {
	function listObject () {
		return "\n      ". $this->field->value;
	}
	function formObject ($object) {
		if ($this->field->value){ $chk  =" CHECKED";} else { $chk  ="";}
		return "\n                     <input type=\"checkbox\" name=\"".$this->frmName($object)."\" $chk >";
	}
}

class HtmlDivEditDateTimeFieldView	extends HtmlEditFieldView {
	function dateFormObject($object){
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "";

		$ret .= "\n                     <div class=\"mday\"><div id=\"".$this->frmName($object) . "mday"."\">";

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mday";
		$ret .= "\" value=\"";
		$ret .= $date["mday"];
		$ret .= "\"> / ";

		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"mon\"><div id=\"".$this->frmName($object) . "mon"."\">";

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mon";
		$ret .= "\" value=\"";
		$ret .= $date["mon"];
		$ret .= "\"> /";

		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"year\"><div id=\"".$this->frmName($object) . "year"."\">";

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "year";
		$ret .= "\" value=\"";
		$ret .= $date["year"];
		$ret .= "\">";

		$ret .= "\n                     </div></div>";
		return $ret;
	}
	function formObject($object){
		return "\n            <div class=\"datefield\">".$this->dateFormObject($object) ."</div>\n            <div class=\"timefield\">". $this->timeFormObject($object)."\n                     </div>";
	}
	function timeFormObject($object) {
		$field = $this->field;
		$date = $field->dateArray();

		$ret = "\n                     <div class=\"hours\"><div id=\"".$this->frmName($object) . "hours"."\">";

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "hours";
		$ret .= "\" value=\"";
		$ret .= $date["hours"];
		$ret .= "\"> :";

		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"minutes\"><div id=\"".$this->frmName($object) . "minutes"."\">";

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "minutes";
		$ret .= "\" value=\"";
		$ret .= $date["minutes"];
		$ret .= "\"> :";

		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"seconds\"><div id=\"".$this->frmName($object) . "seconds"."\">";

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "seconds";
		$ret .= "\" value=\"";
		$ret .= $date["seconds"];
		$ret .= "\">";

		$ret .= "\n                     </div></div>";

		return $ret;
	}
}

class HtmlEditDateTimeFieldView		extends HtmlDivEditDateTimeFieldView {}
class HtmlEditDateFieldView			extends HtmlEditDateTimeFieldView {
	function formObject($object){
		return $this->dateFormObject($object);
	}
}
class HtmlEditTimeFieldView			extends HtmlEditDateTimeFieldView {
	function formObject($object){
		return $this->timeFormObject($object);
	}
}
?>
