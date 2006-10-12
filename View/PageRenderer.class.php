<?php
class PageRenderer // extends PWBObject
{
	var $page;
	var $csss = array ();
	var $defaultViewFactory;
	var $app;

	function PageRenderer(&$app){
		$this->setDefaultViewFactory();
		$this->app =& $app;
	}
	function setDefaultViewFactory(){
		$this->defaultViewFactory =& new HTMLDefaultView;
	}
	function setPage(&$app, &$view){
		$this->page=&$view;
		$view->tagName = 'form';
		$view->setAttribute('action', site_url . '/Action.php');
		$view->setAttribute('method', 'post');
		$view->setAttribute('enctype', 'multipart/form-data');
		$this->addVariable('app_class', getClass($app));
		$this->addVariable('bookmark', $app->urlManager->actUrl);
		$this->addVariable('basedir', basedir);
		$this->addVariable('pwb_url', pwb_url);
	}
	function addVariable($name, $val){
		$n =& new XMLVariable('input', $a=array());
		$n->setAttribute('type', 'hidden');
		$n->setAttribute('id', $name);
		$n->setAttribute('value', $val);
		$n->controller = 1;
		$this->page->appendChild($n);
	}
	function initialPageRenderPage(&$app){
		$initial_page_renderer = & new StandardPageRenderer($app);
		$initial_page_renderer->page=&$app->wholeView;
		echo $initial_page_renderer->renderPage($app);
	}
	function initialRender(){}
	function render(&$page){
		if ($_REQUEST['ajax']=='true'){
			return $this->ajaxRenderPage(&$page);
		} else {
			return $this->renderPage(&$page);
		}
	}
	function ajaxRenderPage($app){
		$initial_page_renderer = & new AjaxPageRenderer($app);
		$initial_page_renderer->page=&$app->wholeView;
		echo $initial_page_renderer->ajaxRenderPage($app);
	}
	function templateExtension(){
		return '.xml';
	}
	function defaultTag(){
		return 'div';
	}
	function viewHandler(){
		return new HTMLHandler;
	}
}

class StandardPageRenderer extends PageRenderer {
	function initializeScripts(&$app) {
		$app->addStdRenderingSpecificScripts();
	}

	function renderPage(&$app) {

		$ret = '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';


	//	$ret = '';
		$ret .= "<html>\n<head><title>" .$this->page->title .	"</title>";
		$ret .= $app->renderExtraHeaderContent();

		foreach ($this->page->style_sheets as $c) {
			$ss = $c[0];
			$ret .= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $ss ;
			if (!$c[1]) {$ret .= '" disabled="disabled';}
			$ret .= "\" />";
		}


		$ret .= '</head><body>';
		if (defined('debugview')&&constant('debugview')=='1'){
			$ret .='<div>' .
						'<form action="'.site_url . '/Action.php" '.
							' method="post"'.
							' enctype="multipart/form-data">' .
							'<input type="hidden" name="app_class" value="'.getClass($app).'" />' .
							'<input type="hidden" name="event_target" value="app" />' .
							'<input type="hidden" name="event" value="reset_templates" />' .
							'Debug View ' .
							'<input type="checkbox" checked="checked" onchange="document.getElementsByTagName(\'link\')[1].disabled = !document.getElementsByTagName(\'link\').item(1).disabled;"/>' .
							'<input type="submit" value="Reload Templates"/>' .
							'<a href="Action.php?restart=yes">Restart application</a>' .
						'</form>'.
						'</div>';
		}
		foreach ($this->page->scripts as $s) {
			$ret .= "\n<script type=\"text/javascript\" src=\"" . $s . "\"></script>";
		}

		$ret .= "\n<script type=\"text/javascript\">";

		foreach ($this->page->jsscripts as $s) {
			  $ret .= $s;
		}
		$ret .= "</script>";

		$page = $this->page->render();
		$ret .= $page;
		$ret .= '</body></html>';

		$this->page->flushModifications();

		return $ret;
	}

	function showXML() {
		$ret = $this->renderPage();
		$ret = str_replace("<", "&lt;", $ret);
		$ret = str_replace(">", "&gt;", $ret);
		$ret = str_replace("\n", "<br/>", $ret);
		$ret = str_replace("   ", "&nbsp;&nbsp;&nbsp;", $ret);
		return $ret;
	}
}

class DebugModificationsPageRenderer extends StandardPageRenderer {
	function renderPage(&$app) {
		//$this->page->updateFullPath();
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		echo $this->page->printString();
		$this->page->flushModifications();
		exit;
	}
}

class DebugPageRenderer extends StandardPageRenderer {
	function renderPage() {
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		echo $this->page->render();
		$this->page->flushModifications();
		exit;
	}
}

class AjaxPageRenderer extends PageRenderer {
	function setPage(&$app, &$page){
		parent::setPage($app, $page);
		$page->setAttribute('onsubmit','postInAjax();');
	}
	function initialRender(){
			$p =& $this->page;
			$p->toFlush = & new ReplaceNodeXMLNodeModification($p, $p);
	}
	function initializeScripts(&$app) {
		$app->addAjaxRenderingSpecificScripts();
	}
	function renderPage(&$page) {
		$this->initialPageRenderPage($page);
	}
	function ajaxRenderPage(&$page) {
		header("Content-type: text/xml");
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		$xml .= "\n<ajax>";
		$xml .= $this->renderAjaxResponseCommands($this->page);
		$xml .= $this->renderAjaxCommands();
		$xml .= "</ajax>";

		$this->page->flushModifications();

		return $xml;
	}

	function renderAjaxCommands() {
		$xml = '';
		foreach (array_keys($this->app->ajaxCommands) as $i) {
			$xml .= $this->app->ajaxCommands[$i]->renderAjaxResponseCommand();
		}

		$a = array();
		$this->app->ajaxCommands =& $a;

		return $xml;
	}

	function renderAjaxResponseCommands(& $node) {
		$xml = '';
		$xml .=$node->toFlush->renderAjaxResponseCommand();
		foreach (array_keys($node->modifications) as $i) {
			$xml .= $node->modifications[$i]->renderAjaxResponseCommand();
		}
		foreach (array_keys($node->childNodes) as $i) {
			$xml .= $this->renderAjaxResponseCommands($node->childNodes[$i]);
		}
		return $xml;
	}
}

class DebugAjaxPageRenderer extends AjaxPageRenderer
{
	function renderActionLinkAction(& $action_link) {
		return 'callAction(&#34;' . $action_link->getId() . '&#34;);';
	}
}
?>
