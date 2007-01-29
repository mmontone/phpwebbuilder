<?php

/*
 * El stream se abre, se renderea el inicio, y se pone un register para cuando se cierre, avisando el fin
 * del stream.
 * Cada elemento del stream es un command.
 * El rendereo chequea por nuevos inputs, manda al dispatcher, y sigue rendereando.
 *
 * Problemas:
 * - Recibir los inputs
 * - renderear con js los commands
 *
 * */


class CometPageRenderer extends PageRenderer {
	function initPage(&$win){
		parent::initPage($win);
		$win->wholeView->setAttribute('onsubmit','refresh();');
	}
	function initialRender(&$win){
		$win->redraw();
	}
	function initializeScripts(&$app) {
		$app->addCometRenderingSpecificScripts();
	}
	function renderPage(&$win){
		#@typecheck $win:Window@#
		return $this->initialPageRenderPage($win);
	}
	function render(&$win){
		#@typecheck $win:Window@#
		if (isset($_REQUEST['ajax'])&&$_REQUEST['ajax']=='true'){
			return $this->cometRenderPage($win);
		} else {
			return parent::render($win);
		}
	}
	function closeComet(){
		echo '<script>' .
				'window.frameElement.ownerDocument.window.closeComet()' .
				'</script></body></html>';
	}
	function debug($str){
			echo '<script>' .
				'alert(\''.$str.'\');' .
				'</script>';
	}
	function cometRenderPage(&$win){
		#@typecheck $win:Window@#
   		register_shutdown_function(array(&$this, "closeComet"));
		echo "<html><body>";
		$x=0;
		while($x++<60){
			if (ActionDispatcher::dispatchComet()){
				$win->wholeView->renderJsResponseCommand();
				echo $this->renderJSCommands($win);
				$x=0;
			}
			usleep(500000);
		}
	}

	function renderJSCommands(&$window) {
		$xml = '';
		foreach (array_keys($window->ajaxCommands) as $i) {
			$xml .= $window->ajaxCommands[$i]->renderJsResponseCommand();
		}
		$a = array();
		$window->ajaxCommands =& $a;

		return $xml;
	}

	function toAjax($s) {
		return $this->toXML($this->toHTML($s));
	}

}
?>