<?php
class PageRenderer // extends PWBObject
{
	var $page;
	var $csss = array ();

	function setPage(&$view){
		$app =& Application::instance();
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
		$initial_page_renderer = & new StandardPageRenderer();
		$initial_page_renderer->page=&$app->wholeView;
		echo $initial_page_renderer->renderPage($app);
	}
	function initialRender(){}
	function render(&$page){
		return $this->renderPage($page);
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
			$ret .= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $c . "\" />";
		}


		$ret .= '</head><body>';

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
	function setPage(&$page){
		parent::setPage($page);
		$page->setAttribute('onsubmit','postInAjax();');
	}
	function initialRender(){
			$p =& $this->page;
			$p->toFlush = & new ReplaceNodeXMLNodeModification($p, $p);
			$p->modifications[] =& $p->toFlush;
	}
	function initializeScripts(&$app) {
		$app->addAjaxRenderingSpecificScripts();
	}
	function render(&$page){
		if ($_REQUEST['ajax']=='true'){
			return $this->renderPage(&$page);
		} else {
			return $this->initialPageRenderPage(&$page);
		}
	}
	function renderPage(&$page) {
		header("Content-type: text/xml");
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		$xml .= "\n<ajax>";
		$xml .= $this->renderAjaxResponseCommands($this->page);
		$xml .= "</ajax>";

		$this->page->flushModifications();

		return $xml;
	}

	function renderAjaxResponseCommands(& $node) {
		$xml = '';

		foreach (array_keys($node->modifications) as $i) {
			if (method_exists($node->modifications[$i],'renderAjaxResponseCommand')){
				$xml .= $node->modifications[$i]->renderAjaxResponseCommand();
			}
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
