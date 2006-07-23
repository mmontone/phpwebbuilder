<?php
class PageRenderer // extends PWBObject
{
	var $page;
	var $csss = array ();

	function setPage(&$view){
		$this->page=&$view;
	}
	function initialPageRenderPage(&$app){
		$initial_page_renderer = & new StandardPageRenderer();
		$initial_page_renderer->setPage($app->wholeView);
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
		$this->page->tagName = 'form';
		$this->page->setAttribute('action', site_url . '/Action.php');
		$this->page->setAttribute('method', 'post');
		$this->page->setAttribute('enctype', 'multipart/form-data');
		$this->page->setAttribute('app', getClass($app));
		$this->page->setAttribute('bookmark', $app->urlManager->actUrl);

		/*
		$ret = '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		*/

		$ret = '';
		$ret .= "<html>\n<head><title>" .$this->page->title .	"</title>";
		$ret .= $app->commonCSS();
		$ret .= $app->renderExtraHeaderContent();

		foreach ($this->page->style_sheets as $c) {
			$ret .= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $c . "\" />";
		}


		/*
		foreach ($this->page->scripts as $s) {
			$ret .= "\n<script type=\"text/javascript\" src=\"" . $s . "\"></script>";
		}*/


		$ret .= '</head><body>';
		$ret .= '<input type="hidden" name="basedir" id="basedir" value="'.basedir.'"/>';

		foreach ($this->page->scripts as $s) {
			$ret .= "\n<script type=\"text/javascript\" src=\"" . $s . "\"></script>";
		}

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
		//$this->page->updateFullPath();
		$xml .= $this->renderAjaxResponseCommands($this->page);
		$xml .= "</ajax>";

		$this->page->flushModifications();

		return $xml;
	}

	function renderAjaxResponseCommands(& $node) {
		$xml = '';

		foreach (array_keys($node->modifications) as $i) {
			$xml .= $this->renderModification($node->modifications[$i]);
		}

		foreach (array_keys($node->childNodes) as $i) {
			$xml .= $this->renderAjaxResponseCommands($node->childNodes[$i]);
		}

		return $xml;
	}

	function renderModification(& $mod) {
		switch (getClass($mod)) {
			case 'replacechildxmlnodemodification' :
				$mod->replacement->flushModifications();
				return $mod->renderAjaxResponseCommand();
			case 'replacenodexmlnodemodification' :
				$mod->replacement->flushModifications();
				return $mod->renderAjaxResponseCommand();
			case 'appendchildxmlnodemodification' :
				$mod->child->flushModifications();
				return $mod->renderAjaxResponseCommand();
			case 'removechildxmlnodemodification' :
				return $mod->renderAjaxResponseCommand();
			case 'setattributexmlnodemodification' :
				return $mod->renderAjaxResponseCommand();
			case 'removeattributexmlnodemodification' :
				return $mod->renderAjaxResponseCommand();
			case 'insertbeforexmlnodemodification' :
				$mod->new->flushModifications();
				return $mod->renderAjaxResponseCommand();
			case '' :
				trace('null object in modifications');
				return '';
			case 'bookmarkxmlnodemodification':
				return $mod->renderAjaxResponseCommand();
			default :
				echo "Not match in AjaxPageRenderer>>renderModification" .
						" for ".getClass($mod);
				exit;
		}
	}
}

class DebugAjaxPageRenderer extends AjaxPageRenderer
{
	function renderActionLinkAction(& $action_link) {
		return 'callAction(&#34;' . $action_link->getId() . '&#34;);';
	}
}
?>