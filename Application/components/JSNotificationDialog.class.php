<?php

class JSNotificationDialog extends JSComponent {
	var $notification;

	function JSNotificationDialog($notification, $callbacks=array()) {
		if (!empty($callbacks)) {
			$this->registerCallbacks($callbacks);
		}
		$this->notification =& $notification;
		parent::JSComponent();
	}

	function main() {
		$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
		return "alert(\"$this->notification\");\n" .
				"callback(\"accept\")";
	}

	function accept() {
		$this->callback('on_accept');
	}
}
?>