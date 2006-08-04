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
		$app =& Application::Instance();
		return $app->wholeView->jsscripts[$this->getId()];
	}

	function start() {
		$app =& Application::Instance();
		$app->wholeView->jsscripts[$this->getId()] = '';
		$this->render();
	}

	function stop() {
		$app =& Application::Instance();
		unset($app->wholeView->jsscripts[$this->getId()]);
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
			$this->registered_callbacks[$callback]->callWith($params);
		}
	}
}

?>