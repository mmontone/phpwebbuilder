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
	}
	function  rendersAjax(){
		return true;
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
   		$maxsecs=5;       //seconds
		$this->ad =& ActionDispatcher::initializeComet();
		$this->sendHeaders();
		#@profile xdebug_start_profiling();@#
		while(true){
			$this->ad->dispatchComet();
			set_time_limit($maxsecs);
			$this->startCometWrapper();
			$this->renderWindow($win);
			$win->toFlush =& new ChildModificationsXMLNodeModification($this);
			$win->modWindows();
			$this->renderJSCommands($win);
			$this->stopCometWrapper();
			#@profile echo '<div id="profile">';xdebug_dump_function_profile(4);echo '</div>';@#
			flush();
			if($win->closeStream) {break;}
			#@profile xdebug_stop_profiling();@#
		}
		$this->sendFooters();
	}
	function sendHeaders(){
		echo '<script>parWin = window.frameElement.ownerDocument.window;parWin.cometStarted();</script>';
		 #@profile echo '<a href="" onclick="parWin.closeComet()">close comet</a>'; return; @#
		echo '<script>window.onload=function(){parWin.closeComet();};</script>';

	}
	function sendFooters(){}
	function renderWindow(&$win){
		$win->wholeView->renderJsResponseCommand();
	}
	function startCometWrapper(){}
	function stopCometWrapper(){
			#@profile echo '<script>var prof = document.getElementById("profile");if (prof)prof.parentNode.removeChild(prof);</script>';@#
			flush();

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
/*
class XulCometPageRenderer extends CometPageRenderer{
function sendHeaders(){
		header('Content-type: multipart/x-mixed-replace;boundary="rn9012"');
		$this->startCometWrapper();
		echo '<script>parWin = window;</script>';
		$this->stopCometWrapper();
	}
	function sendFooters(){
		$this->startCometWrapper();
		echo '<script>xrequest = null;</script>';
		$this->stopCometWrapper();
		echo "--\n";

	}
	function renderWindow(&$win){
		//$win->wholeView->renderJsResponseCommand();
		echo $win->wholeView->renderAjaxResponseCommand();
	}
	function startCometWrapper(){
  		echo "Content-type: text/xml\n\n"; echo '<?xml version="1.0"?'.'><ajax>';
	}
	function stopCometWrapper(){
  		echo '</ajax>';echo "\n--rn9012\n";flush();
	}
}

*/
?>