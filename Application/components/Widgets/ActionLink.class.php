<?php

require_once dirname(__FILE__) . '/Widget.class.php';

class ActionLink extends CommandLink
{
	var $target;
	var $action;
	var $params;
	var $token;
	function ActionLink (&$target, $action, $text, &$params) {
		parent::CommandLink(array('text'=>$text,
				'proceedFunction'=>new FunctionObject($target, $action, $params))
		);
	}
}

?>