<?php

class ApplicationErrorHandler extends Component {
	var $output;

	function ApplicationErrorHandler($output) {
		$this->output =& $output;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new CommandLink(array('text' => 'Restart application', 'proceedFunction' => new FunctionObject($this, 'restartApplication'))),'restart_application');
	}

	function restartApplication() {

	}
}

class DevelopApplicationErrorHandler extends Component {
	function initialize() {
		$this->addComponent(new Label($this->output), 'out');
		parent::initialize();
	}
}

class DeployedApplicationErrorHandler extends ApplicationErrorHandler {
	function initialize() {
		$this->addComponent(new CommandLink(array('text' => 'See details', 'proceedFunction' => new FunctionObject($this, 'seeDetails' ))), 'see_details');
		$this->addComponent(new CommandLink(array('text' => 'Notify administrators', 'proceedFunction' => new FunctionObject($this, 'notifyAdministrators' ))), 'notify_administrators');
		parent::initialize();
	}

	function notifyAdministrators() {
		$this->notify_administrators->delete();
		$this->addComponent(new BugNotifier($this->output), 'bug_notifier');
	}

	function seeDetails() {
		$this->addComponent(new TextArea($this->output), 'out');
		$this->addComponent(new CommandLink(array('text' => 'Hide details', 'proceedFunction' => new FunctionObject($this, 'hideDetails' ))), 'see_details');
	}

	function hideDetails() {
		$this->out->delete();
		$this->addComponent(new CommandLink(array('text' => 'See details', 'proceedFunction' => new FunctionObject($this, 'seeDetails' ))), 'see_details');
	}

}

class BugNotifier extends Component {
	var $comment = '';
	var $user_name;
	var $user_lastname;
	var $output;

	function BugNotifier($output) {
		$this->output =& $output;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new TextArea($this->comment), 'comment_area');
		$this->addComponent(new CommandLink(array('text' => 'Enviar', 'proceedFunction' => new FunctionObject($this, 'notifyAdministrators' ))), 'notify_administrators');
	}

	function notifyAdministrators() {
		$app =& Application::instance();

		$ok = mail($app->getAdminEmail(), $app->getName() . ' bug!!', $this->output);

		if ($ok) {
			$this->stopAndCall(new NotificationDialog('Bug notification sent successfully'));
		}
		else {
			$this->stopAndCall(new NotificationDialog('The bug notification couldn\'t be sent'));
		}
	}
}

?>