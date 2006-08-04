<?php

class JSQuestionDialog extends JSComponent {
	var $question;

	function JSQuestionDialog($question, $callbacks=array()) {
		if (!empty($callbacks)) {
			$this->registerCallbacks($callbacks);
		}
		$this->question =& $question;
		parent::JSComponent();
	}

	function main() {
		// Adding interest should be automatic parsing the callback parameter
		$this->addInterestIn('yes', new FunctionObject($this, 'yes'));
		$this->addInterestIn('no', new FunctionObject($this, 'no'));

		return "if(confirm(\"$this->question\")) {
					callback(\"yes\");
				}
				else {
					callback(\"no\");
				}";
	}

	function yes() {
		$this->callback('on_yes');
	}

	function no() {
		$this->callback('on_no');
	}
}

?>