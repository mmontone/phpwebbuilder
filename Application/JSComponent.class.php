<?php

class JSComponent extends Component {
	function renderFunction($function) {
		$body = $this->$function();
		$id = $this->getId();
		$body = preg_replace("/callback\(([_A-Za-z0-9-\,\"]*)\)/","callback(\"$id\", \\1)",$body);
		$area =& $this->getRenderingArea();
		$jsfunc =& $this->getFunctionName($function);
		$area .= "\nfunction $jsfunc() {\n" .
				"$body\n" .
				"}";
	}

	function &getRenderingArea() {
		$app =& Window::getActiveInstance();
		return $app->jsscripts[$this->getId()];
	}

	function start() {
		$app =& Window::getActiveInstance();
		$app->jsscripts[$this->getId()] = '';
		$this->render();
	}

	function stop() {
		$app =& Window::getActiveInstance();
		unset($app->jsscripts[$this->getId()]);
	}

	function render() {
		$this->renderFunction('main');
	}

	function getFunctionName($function) {
		return $this->getFunctionPrefix() . "_$function";
	}

	function getMainFunction() {
		return $this->getFunctionName('main');
	}

	function getFunctionPrefix() {
		return preg_replace("/\//", "_", $this->getId());
	}

	function call() {

	}

	function callback($callback=null, $params=array()) {
		if (($callback != null) and ($this->registered_callbacks[$callback] != null)) {
			$this->registered_callbacks[$callback]->executeWith($params);
		}
	}
}

?>