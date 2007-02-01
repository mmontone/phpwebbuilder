<?php

class XULPageRenderer extends PageRenderer {
	function defaultTag(){
		return 'box';
	}
	function initializeScripts(&$app) {
		$app->addXULRenderingSpecificScripts();
		//$app->addAjaxRenderingSpecificScripts();
	}
	function initPage(&$win){
		#@typecheck $win:Window@#
		$view =& $win->wholeView;
		$view->setTagName('box');
		$this->addVariable($view,'app_class', getClass($win->parent));
		$this->addVariable($view,'bookmark', $app->urlManager->actUrl);
		$this->addVariable($view,'basedir', basedir);
		$this->addVariable($view,'pwb_url', pwb_url);
		$this->addVariable($view,'window', $win->owner_index());
	}
	function addVariable(&$view,$name, $val){
		$n =& new XMLVariable('textbox', $a=array());
		$n->setAttribute('hidden', 'true');
		$n->setAttribute('id', $name);
		$n->setAttribute('value', $val);
		$n->controller = 1;
		$view->appendChild($n);
	}
	function renderPage(&$win) {
		#@typecheck $win:Window@#
		#@typecheck $win->wholeView:XMLNode@#
		header("Content-type: application/vnd.mozilla.xul+xml");
		$ret = '<?xml version="1.0"?>
		<?xml-stylesheet href="chrome://global/skin/" type="text/css" ?>';
		foreach ($this->app->style_sheets as $c) {
			foreach($c as $k=>$v) {
				$d .= $k.'="'.$v.'" ';
			}
			$ret .= '<?xml-stylesheet href="' . $d . '" type="text/css" ?>';
		}
		$ret .='<window id="main-window"
    title="'.$win->wholeView->title .'"
    orient="horizontal" '
	.'xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" '
	.'xmlns:html="http://www.w3.org/1999/xhtml" '
	.'>';
	if (defined('debugview')&&constant('debugview')=='1'){
			$ret .=
						'<box>'.
							'<label>Debug View</label>' .
							'<checkbox label="disable" checked="true" oncommand="document.styleSheets.item(2).disabled = !document.styleSheets.item(2).disabled;"/>' .
							'<button oncommand="sendEvent(\'reset_templates\', document.getElementById(\'app\'));" label="Reload Templates"/>' .
							'<button oncommand="sendUpdate(new Update(\'restart\', \'yes\'));" label="Restart application"/>' .
						'</box>';
		}


		foreach ($this->app->scripts as $s) {
			$ret .= '<script type="application/x-javascript" src="' . $s . '"/>';
		}

		$ret .= '<script type="application/x-javascript">';

		foreach ($this->app->jsscripts as $s) {
			  $ret .= $s;
		}
		$ret .= "</script>";

		$page = $win->wholeView->render();
		$ret .= $page;
		$ret .= '</window>';
		return $ret;
	}
	function templateExtension(){
		return '.xul';
	}
	function &viewHandler(){
		return new XULHandler;
	}
	function addTemplateName(&$view, $name){
		$view->setAttribute('tooltiptext', $name);
	}


}
?>
