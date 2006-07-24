<?php

class UITests extends Component {
	function initialize(){
		$menu =& $this->addComponent(new ActionMenu, 'menu');
		$this->addMenuItems($menu);
	}

	function addMenuItems(&$menu) {
		$action =& new FunctionObject($this, 'menuItemSelected');
		$menu->addMenuItem(new ActionMenuItem('Input', $action));
	}

 	function menuItemSelected(&$menuitem) {
 		$test =& new $menuitem->label . 'Test';
 		$this->addComponent($test, 'test');
 	}
}

class ActionMenu extends Component {
	function addMenuItem(&$menuitem) {
		$this->addComponent($menuitem);
	}
}

class ActionMenuItem extends Component {
	var $label;
	var $action;

	function ActionMenuItem($label, &$action) {
		$this->label =& $label;
		$this->action =& $action;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new ActionLink2(array('action'=>new FunctionObject($this, 'execute'),'text'=>$this->label)));
	}

	function execute() {
		return $action->callWith($this);
	}
}

class InputTest extends Component {
	function initialize() {
		$this->addComponent(new Label('Test this input:'));
		$this->addComponent(new Input());
	}
}
?>