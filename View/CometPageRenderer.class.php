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
				'parWin.closeComet()' .
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
		echo '<html><body><script>parWin = window.frameElement.ownerDocument.window;</script>';
		$x=0;
		$ad =& ActionDispatcher::initializeComet();
		while($x++<120){
			if ($count = $ad->dispatchComet()){
				$win->wholeView->renderJsResponseCommand();
				echo '<script>';
				echo $this->renderJSCommands($win);
				echo 'parWin.loadingStop(' .
					'parWin.cometCount-='.$count.');' .
				'</script>';
				flush();
				$x=0;
			}
			usleep(500000);
		}
	}

	function renderJSCommands(&$window) {
		foreach (array_keys($window->ajaxCommands) as $i) {
			$xml .= 'parWin.'.$window->ajaxCommands[$i]->renderStdResponseCommand();
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