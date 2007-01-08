<?php


class CommandLinkXULHandler extends WidgetXULHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->setTagName('button');
        //$v->setAttribute('label', $this->component->textv);
        $v->setAttribute('label', $this->component->textv->getValue());
        return $v;
	}
}

?>