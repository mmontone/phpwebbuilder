<?php

class ApplicationErrorHandler extends Component {
	var $error;
	var $backtrace;

	function ApplicationErrorHandler() {
		$this->error =& new ValueHolder('');
		$this->backtrace =& new ValueHolder('');

		parent::Component();
	}
	function getError() {
		return $this->error->getValue();
	}

	function getBacktrace() {
		return $this->backtrace->getValue();
	}

	function setError($error) {
		$this->error->setValue($error);
	}

	function setBacktrace($backtrace) {
		$this->backtrace->setValue($backtrace);
	}

	function initialize() {
		$this->addComponent(new Link(site_url . '?restart=yes', 'Restart application'),'restart_application');
	}
	function mail($to, $subject, $message){
		mail($to, $subject, $message);
	}
}

class DevelopApplicationErrorHandler extends ApplicationErrorHandler {
	function initialize() {
		$this->addComponent(new DetailsPanel($this->error, $this->backtrace), 'details_panel');

		parent::initialize();
	}
}

class DeployedApplicationErrorHandler extends ApplicationErrorHandler {
	function initialize() {
		$this->addComponent(new CommandLink(array('text' => '(See details)', 'proceedFunction' => new FunctionObject($this, 'seeDetails' ))), 'see_details');
		$this->addComponent(new CommandLink(array('text' => 'Notify administrators', 'proceedFunction' => new FunctionObject($this, 'notifyAdministrators' ))), 'notify_administrators');
		parent::initialize();
	}

	function notifyAdministrators() {
		$this->notify_administrators->delete();
		$bugnotifier =& new BugNotifier($this->error, $this->backtrace, $this);
		$bugnotifier->registerCallback('notification_sent', new FunctionObject($this, 'closeBugNotifier'));
		$this->addComponent($bugnotifier, 'bug_notifier');
	}

	function closeBugNotifier() {
		$this->bug_notifier->delete();
	}

	function seeDetails() {
		$this->addComponent(new DetailsPanel($this->error, $this->backtrace), 'details_panel');
		$this->addComponent(new CommandLink(array('text' => '(Hide details)', 'proceedFunction' => new FunctionObject($this, 'hideDetails' ))), 'see_details');
	}

	function hideDetails() {
		$this->details_panel->delete();
		$this->addComponent(new CommandLink(array('text' => '(See details)', 'proceedFunction' => new FunctionObject($this, 'seeDetails' ))), 'see_details');
	}
}

class BugNotifier extends Component {
	var $comment;
	var $user_name;
	var $user_lastname;
	var $error;
	var $backtrace;

	function BugNotifier($error, $backtrace, $handler) {
		$this->error =& $error;
		$this->backtrace =& $backtrace;
		$this->comment =& new ValueHolder('');
		$this->user_name =& new ValueHolder('');
		$this->user_lastname =& new ValueHolder('');
		$this->handler =& $handler;

		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Input($this->user_name), 'name_input');
		$this->addComponent(new Input($this->user_lastname), 'lastname_input');
		$this->addComponent(new TextAreaComponent($this->comment), 'comment_area');
		$this->addComponent(new CommandLink(array('text' => 'Send', 'proceedFunction' => new FunctionObject($this, 'notifyAdministrators' ))), 'notify_administrators');
	}

	function notifyAdministrators() {
		$app =& Application::instance();

		$ok = $this->handler->mail($app->getAdminEmail(), $app->getName() . ' bug!!', $this->getNotification());

		if ($ok) {
			$notification =& new NotificationDialog('Bug notification sent successfully');
			$notification->registerCallback('on_accept', new FunctionObject($this, 'notificationSent'));
			$this->call($notification);
		}
		else {
			$this->call(new NotificationDialog('The bug notification couldn\'t be sent. Try again. If the problem persists, contact the administrator'));
		}
	}

	function getNotification() {
		$username =& $this->user_name->getValue();
		$lastname =& $this->user_lastname->getValue();
		$comments =& $this->comment->getValue();

		return "User: $username $lastname\n" .
		       "Error: $this->error\n" .
		       "Backtrace: $this->backtrace\n" .
		       "Comments: $comment";
	}

	function notificationSent() {
		$this->callback('notification_sent');
	}
}

class DetailsPanel extends Component {
	var $error;
	var $backtrace;

	function DetailsPanel(&$error, &$backtrace) {
		$this->error =& $error;
		$this->backtrace =& $backtrace;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Text($this->error), 'error_area');
		$this->addComponent(new TextAreaComponent($this->backtrace), 'backtrace_area');
	}
}

?>