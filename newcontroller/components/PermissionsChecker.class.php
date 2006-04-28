<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class PermissionsChecker extends Component
{
    var $target;

    function PermissionsChecker(&$component) {
        $component->addEventListener(array('on add_action' => 'checkPermission',
                                           'on edit_action' => 'checkPermission',
                                           'on delete_action' => 'checkPermission'));

    }

    function checkPermission(&$signaler, $params) {
    	$action = $params['action'];
        $user =& User::instance();
        if (!$user->hasPermission($component->model, $action)) {
            $this->target =& $signaler;
           	$signaler->stopAndCall($this);
        }
    }

    function start() {
    	$this->notify('You dont have enough permissions to do that');
    }

    function notification_accepted() {
    	$this->stopAndCall($this->target);
    }
}

?>