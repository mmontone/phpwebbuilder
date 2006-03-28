<?

require_once dirname(__FILE__) . '/../../../OldViews/Action/AbstractFieldView.class.php';

class HtmlReadFieldView extends AbstractFieldView {
	function readForm (&$object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name])) {
			$val = $this->field->trim($form[$name]);
			$this->field->setValue($val);
		}
		return $this->field->check($val);
	}
	function frmName(&$object) {
		$html =& new HtmlReadView;
		$html =& $html->viewFor($object);
		return $html->formName() . $this->field->colName;
	}
	function &visitedTextField(&$field) {
		$view =& new HtmlReadTextFieldView;
		$view->field =& $field;
		return $view;
	}

	function &visitedDateTimeField(&$field) {
		$view =& new HtmlReadDateTimeFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedEnumField(&$field) {
		$view =& new HtmlReadEnumFieldView;
		$view->field =& $field;
		return $view;
	}
		function &visitedEmbedField(&$field) {
		$view =& new HtmlReadEmbedFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedTimeField(&$field) {
		$view =& new HtmlReadTimeFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedDateField(&$field) {
		$view =& new HtmlReadDateFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedCollectionField(&$field) {
		$view =& new HtmlReadCollectionFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedPasswordField(&$field) {
		$view =& new HtmlReadPasswordFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedEmailField(&$field) {
		$view =& new HtmlReadEmailFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedTextArea(&$field) {
		$view =& new HtmlReadTextAreaView;
		$view->field =& $field;
		return $view;
	}

       	function &visitedWikiArea(&$field) {
          $view =& new HtmlReadTextAreaView;
          $view->field =& $field;
          return $view;
	}

	function &visitedBoolField(&$field) {
		$view =& new HtmlReadBoolFieldView	;
		$view->field =& $field;
		return $view;
	}
	function &visitedNumField(&$field) {
		$view =& new HtmlReadNumFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedIdField(&$field) {
		$view =& new HtmlReadIdFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedSuperField(&$field) {
		$view =& new HtmlReadIdFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedIndexField(&$field) {
		$view =& new HtmlReadIndexFieldView;
		$view->field =& $field;
		return $view;
	}
	function &visitedUserField(&$field) {
		$view =& new HtmlReadUserFieldView;
		$view->field =& $field;
		return $view;
	}

    function &visitedBlobField(&$field) {
		$view =& new HtmlReadBlobFieldView;
		$view->field =& $field;
		return $view;
	}

	function &visitedFilenameField(&$field) {
		$view =& new HtmlReadFilenameFieldView;
		$view->field =& $field;
		return $view;
	}

    function &visitedHtmlArea(&$field) {
        $view =& new HtmlReadHtmlAreaView;
        $view->field =& $field;
        return $view;
    }
}

class HtmlReadNumFieldView extends HtmlReadFieldView {
}

class HtmlReadTextFieldView extends HtmlReadFieldView {
}
class HtmlReadTextAreaView extends HtmlReadFieldView {}

class HtmlReadHtmlAreaView extends HtmlReadTextAreaView {}

class HtmlReadFilenameFieldView extends HtmlReadTextFieldView {}

class HtmlReadBlobFieldView extends HtmlReadFieldView {}

class HtmlReadIndexFieldView extends HtmlReadFieldView {
}
class HtmlReadUserFieldView extends HtmlReadIndexFieldView {
}

class HtmlReadEnumFieldView extends HtmlReadTextFieldView {
}
class HtmlReadEmbedFieldView extends HtmlReadFieldView {
	function readForm(){}

}



class HtmlReadDateTimeFieldView extends HtmlReadFieldView {
	function readForm ($object, $form) {
		$this->field->setValue($this->dateReadForm ($object, $form) ." ". $this->timeReadForm ($object, $form));
		return TRUE;
	}
	function timeReadForm ($object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name. "hours"])) {
			$date["hours"] = $form[$name. "hours"];
			$date["minutes"] = $form[$name. "minutes"];
			$date["seconds"] = $form[$name. "seconds"];
			return $this->field->timeformat($date);
		}
	}
	function dateReadForm($object, $form){
		$name = $this->frmName($object);
		if (isset($form[$name. "mday"])) {
			$date["mday"] = $form[$name. "mday"];
			$date["year"] = $form[$name. "year"];
			$date["mon"] = $form[$name. "mon"];
			return $this->field->dateformat($date);
		}
	}

}

class HtmlReadDateFieldView extends HtmlReadDateTimeFieldView  {
	function readForm ($object, $form) {
		$this->field->setValue($this->dateReadForm ($object, $form));
		return TRUE;
	}
}

class HtmlReadTimeFieldView extends HtmlReadDateTimeFieldView  {
	function readForm ($object, $form) {
		$this->field->setValue($this->timeReadForm ($object, $form));
		return TRUE;
	}
}


class HtmlReadEmailFieldView extends HtmlReadTextFieldView {}

class HtmlReadPasswordFieldView extends HtmlReadTextFieldView {
	function readForm (&$object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name."1"])) {
			if ($form[$name."1"] == $form[$name."2"]){
				if ($form[$name."1"] == "") {
						$this->field->setValue("");
				} else {
						$val = $this->field->trim($form[$name."1"]);
						$this->field->setValue($val);
				}
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
}


class HtmlReadCollectionFieldView extends HtmlReadFieldView {
}


class HtmlReadIdFieldView extends HtmlReadFieldView {
}



class HtmlReadBoolFieldView extends HtmlReadFieldView {
	function readForm (&$object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name]) AND ($form[$name]=="on")) {$this->field->value=1; } else {$this->field->value=0; };
		return $this->field->check($this->field->value);
	}
}

?>