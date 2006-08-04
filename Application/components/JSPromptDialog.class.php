<?php

class JSPromptDialog extends JSComponent {
	var $message;

	function JSPromptDialog($message, $callbacks=array()) {
		if (!empty($callbacks)) {
			$this->registerCallbacks($callbacks);
		}
		$this->message =& $message;
		parent::JSComponent();
	}

	function main() {
		$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
		return "var res = prompt(\"$this->message\");\n" .
				"callback(\"accept\")";
	}

	function accept() {
		$this->callback('on_accept');
	}
}
?>