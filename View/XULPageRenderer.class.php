<?php

class XULPageRenderer extends PageRenderer {
	function defaultTag(){
		return 'box';
	}
	function initializeScripts(&$app) {
		$app->addXULRenderingSpecificScripts();
	}
	function setDefaultViewFactory(){
		$this->defaultViewFactory =& new XULDefaultView;
	}
	function setPage(&$view){
		$app =& Application::instance();
		$this->page=&$view;
		$view->setTagName('box');
		$this->addVariable('app_class', getClass($app));
		$this->addVariable('bookmark', $app->urlManager->actUrl);
		$this->addVariable('basedir', basedir);
		$this->addVariable('pwb_url', pwb_url);
	}
	function addVariable($name, $val){
		$n =& new XMLVariable('textbox', $a=array());
		$n->setAttribute('hidden', 'true');
		$n->setAttribute('id', $name);
		$n->setAttribute('value', $val);
		$n->controller = 1;
		$this->page->appendChild($n);
	}
	function renderPage(&$app) {
		header("Content-type: application/vnd.mozilla.xul+xml");
		$ret = '<?xml version="1.0"?>
		<?xml-stylesheet href="chrome://global/skin/" type="text/css" ?>';
		foreach ($this->page->style_sheets as $c) {
			$ret .= '<?xml-stylesheet href="' . $c . '" type="text/css" ?>';
		}
		$ret .='<window id="findfile-window"
    title="Hello worls"
    orient="horizontal" '
	.'xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" '
	//.'xmlns:html="http://www.w3.org/1999/xhtml" '
	.'>';



		foreach ($this->page->scripts as $s) {
			$ret .= '<script type="application/x-javascript" src="' . $s . '"/>';
		}

		$ret .= '<script type="application/x-javascript">';

		foreach ($this->page->jsscripts as $s) {
			  $ret .= $s;
		}
		$ret .= "</script>";

		$page = $this->page->render();
		$ret .= $page;
		$ret .= '</window>';
		$this->page->flushModifications();

		return $ret;
	}
	function templateExtension(){
		return '.xul';
	}

}
?>
