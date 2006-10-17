<?php


class CommandLinkXULHandler extends WidgetXULHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->setAttribute('label', $this->component->textv);
		$v->setTagName('button');
		return $v;
	}
}

?>