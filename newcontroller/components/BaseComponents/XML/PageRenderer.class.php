<?php
class PageRenderer // extends PWBObject
{
	var $page;
	var $csss = array ();

	function PageRenderer(& $page) {
		$this->page = & $page;
	}
	function setPage(&$view){
		$this->page=&$view;
	}
}

class StandardPageRenderer extends PageRenderer {
	function StandardPageRenderer(& $page) {
		parent :: PageRenderer($page);
	}

	function renderPage() {
		$this->page->tagName = 'form';
		$this->page->setAttribute('action', 'new_dispatch.php');
		$this->page->setAttribute('method', 'post');
		$this->page->setAttribute('enctype', 'multipart/form-data');

		/*
		$ret = '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		*/

		$ret = '';
		$ret .= "<html>\n<head><title>" .$this->page->title .	"</title>";

		foreach ($this->page->style_sheets as $c) {
			$ret .= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $c . "\" />";
		}


		/*
		foreach ($this->page->scripts as $s) {
			$ret .= "\n<script type=\"text/javascript\" src=\"" . $s . "\"></script>";
		}*/


		$ret .= '</head><body>';

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

	function renderActionLinkAction(& $action_link) {
		return 'callAction(&#34;' . $action_link->getId() . '&#34;);';
	}
}

class DebugModificationsPageRenderer extends StandardPageRenderer {
	function renderPage() {
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
	function AjaxRenderer(& $page) {
		parent :: PageRenderer($page);
	}
	function setPage(&$page){
		parent::setPage($page);
		$page->setAttribute('onsubmit','postInAjax();');
	}
	function renderPage() {
		header("Content-type: text/xml");
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		$xml .= "\n<ajax_response>";
		//$this->page->updateFullPath();
		$xml .= $this->renderAjaxResponseCommands($this->page);
		$xml .= "</ajax_response>";

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
		switch (get_class($mod)) {
			case 'replacechildxmlnodemodification' :
				$mod->replacement->flushModifications();
				return $mod->renderAjaxResponseCommand();
			case 'appendchildxmlnodemodification' :
				$mod->child->flushModifications();
				return $mod->renderAjaxResponseCommand();
			case 'removechildxmlnodemodification' :
				return $mod->renderAjaxResponseCommand();
			case 'setattributexmlnodemodification' :
				return $mod->renderAjaxResponseCommand();
			case 'insertbeforexmlnodemodification' :
				$mod->new->flushModifications();
				return $mod->renderAjaxResponseCommand();
		}
	}

	function renderActionLinkAction(& $action_link) {
		return 'callActionAjax(&#34;' . $action_link->getId() . '&#34;);';
	}
}

class DebugAjaxPageRenderer extends AjaxPageRenderer
{
	function renderActionLinkAction(& $action_link) {
		return 'callAction(&#34;' . $action_link->getId() . '&#34;);';
	}
}
?>