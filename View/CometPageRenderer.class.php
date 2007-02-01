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
	function CometPageRenderer(&$app){
		parent::PageRenderer($app);
		$this->ad =& ActionDispatcher::initializeComet();
	}
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
	function debug($str){
			echo '<script>' .
				'alert(\''.$str.'\');' .
				'</script>';
	}
	function cometRenderPage(&$win){
		#@typecheck $win:Window@#
   		$interval=10000; //microseconds
   		$maxsecs=20;       //seconds
   		$maxtime=$maxsecs*1000000/$interval;
		echo '<html><body><script>parWin = window.frameElement.ownerDocument.window;window.onload=function(){parWin.closeComet();};</script>';
		$x=0;
		while($x++<$maxtime && !connection_aborted()){
			if ($count = $this->ad->dispatchComet()){
				$win->wholeView->renderJsResponseCommand();
				$win->toFlush =& new ChildModificationsXMLNodeModification($this);
				$win->modWindows();
				$this->renderJSCommands($win);
				if($win->closeStream) {$this->closeComet(); return;}
				$x=0;
				set_time_limit($maxsecs);
			}
			flush();
			usleep($interval);
		}
	}

	function renderJSCommands(&$window) {

		foreach (array_keys($window->ajaxCommands) as $i) {
			echo '<script>';
			echo 'parWin.'.$window->ajaxCommands[$i]->renderStdResponseCommand();
			echo '</script>';

		}
		flush();
		$a = array();
		$window->ajaxCommands =& $a;
	}

	function toAjax($s) {
		return $this->toXML($this->toHTML($s));
	}

}
?>