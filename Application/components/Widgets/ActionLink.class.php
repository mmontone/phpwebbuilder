<?php

class ActionLink extends CommandLink
{
	function ActionLink (&$target, $action, $text, &$params) {
		parent::CommandLink(array('text'=>$text,
				'proceedFunction'=>new FunctionObject($target, $action, $params))
		);
	}
}

?>