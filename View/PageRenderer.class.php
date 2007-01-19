<?php
class PageRenderer // extends PWBObject
{
	var $page;
	var $csss = array ();
	var $defaultViewFactory;
	var $app;

	function PageRenderer(&$app){
		#@typecheck $app:Application@#
		$this->app =& $app;
	}
	function &create(&$app){
		$v = null;
		if (isset($_REQUEST['render'])) {
			$page_renderer= $_REQUEST['render'].'PageRenderer';
		} else {
			$page_renderer = constant('page_renderer');
		}
		$v =& new $page_renderer($app);
		return $v;
	}
	function initPage(&$win){
		#@typecheck $win:Window@#
		$view =& $win->wholeView;
		$view->tagName = 'form';
		$view->setAttribute('action', site_url . 'Action.php?'.SID);
		$view->setAttribute('method', 'post');
		$view->setAttribute('enctype', 'multipart/form-data');
		$this->addVariable($view,'app_class', getClass($win->parent));
		$this->addVariable($view,'bookmark', $win->urlManager->actUrl);
		$this->addVariable($view,'basedir', basedir);
		$this->addVariable($view,'pwb_url', pwb_url);
		$this->addVariable($view,'window', $win->owner_index());
	}
	function addVariable(&$view,$name, $val){
		$n =& new XMLVariable('input', $a=array());
		$n->setAttribute('type', 'hidden');
		$n->setAttribute('id', $name);
		$n->setAttribute('value', $val);
		$n->controller = 1;
		$view->appendChild($n);
	}
	function initialPageRenderPage(&$win){
		#@typecheck $win:Window@#
		$initial_page_renderer = & new StandardPageRenderer(Application::Instance());
		return $initial_page_renderer->renderPage($win);
	}
	function initialRender(){}
	function render(&$win){
		#@typecheck $win:Window@#
		if (isset($_REQUEST['ajax'])&&$_REQUEST['ajax']=='true'){
			return $this->ajaxRenderPage($win);
		} else {
			return $this->renderPage($win);
		}
	}
	function ajaxRenderPage(&$win){
		#@typecheck $win:Window@#
		$initial_page_renderer = & new AjaxPageRenderer(Application::Instance());
		return $initial_page_renderer->renderPage($win);
	}
	function templateExtension(){
		return '.xml';
	}
	function defaultTag(){
		return 'div';
	}
	function &viewHandler(){
		return new HTMLHandler;
	}
	function addTemplateName(&$view, $name){
		$t =& new XMLTextNode($name);
		$tn =& new XMLNodeModificationsTracker();
		$tn->appendChild($t);
		$tn->addCSSClass('templateName');
		$fc =& $view->first_child();
		if ($fc!==null){
			$view->insertBefore($fc,$tn);
		} else {
			$view->appendChild($tn);
		}
	}
	/**
	 * encodes the string to valid XHTML
	 *
	 * http://www.htmlhelp.com/reference/html40/entities/
	 */

	function toHTML($s) {
		$s = str_replace('&', '&amp;', $s);
		$s = str_replace('ñ', '&ntilde;', $s);
		$s = str_replace('¿', '&iquest;', $s);
		$s = str_replace('Ñ', '&Ntilde;', $s);
		$s = str_replace('á', '&aacute;', $s);
		$s = str_replace('é', '&eacute;', $s);
		$s = str_replace('í', '&iacute;', $s);
		$s = str_replace('ó', '&oacute;', $s);
		$s = str_replace('ú', '&uacute;', $s);
		$s = str_replace('Á', '&Aacute;', $s);
		$s = str_replace('É', '&Eacute;', $s);
		$s = str_replace('Í', '&Iacute;', $s);
		$s = str_replace('Ó', '&Ooacute;', $s);
		$s = str_replace('Ú', '&Uacute;', $s);
		$s = str_replace('º', '&ordm;', $s);
		$s = str_replace('ª', '&ordf;', $s);
		$s = htmlentities($s);
		return $s;
		//return mb_convert_encoding($s,"HTML-ENTITIES","auto");
	}

	/**
	 * Encodes the string in valid XML
	 *
	 * http://www.htmlhelp.com/reference/html40/entities/
	 */

	function toXML($s) {
		$s = str_replace('&', '&amp;', $s);
		$s = ereg_replace('&(amp;|&amp;|#38;|&#38;)+([A-Za-z0-9#]+;)', '&\\2', $s);
		$s = str_replace('>', '&#62;', $s);
		$s = str_replace('&gt;', '&#62;', $s);
		$s = str_replace('<', '&#60;', $s);
		$s = str_replace('&lt;', '&#60;', $s);
		$s = str_replace('"', '&#34;', $s);
		//$s = str_replace('|', '&#166;', $s);
		$s = str_replace('&amp;', '&#38;', $s);
		$s = str_replace('&iquest;', '&#191;', $s);
		$s = str_replace('&aacute;', '&#225;', $s);
		$s = str_replace('&eacute;', '&#233;', $s);
		$s = str_replace('&iacute;', '&#237;', $s);
		$s = str_replace('&oacute;', '&#243;', $s);
		$s = str_replace('&uacute;', '&#250;', $s);
		$s = str_replace('&uuml;', '&#252;', $s);
		$s = str_replace('&ntilde;', '&#241;', $s);
		$s = str_replace('&Aacute;', '&#193;', $s);
		$s = str_replace('&Eacute;', '&#201;', $s);
		$s = str_replace('&Iacute;', '&#205;', $s);
		$s = str_replace('&Oacute;', '&#211;', $s);
		$s = str_replace('&Uacute;', '&#218;', $s);
		$s = str_replace('&Uuml;', '&#220;', $s);
		$s = str_replace('&Ntilde;', '&#209;', $s);
		$s = str_replace('&quote;', '&#34;', $s);
		$s = str_replace('&nbsp;', '&#160;', $s);
		$s = str_replace('&ordm;', '&#186;', $s);
		$s = str_replace('&ordf;', '&#170;', $s);
		$s = ereg_replace('&([A-Za-z0-9]+);', '&#38;\\1;', $s);
		return $s;
	}
	function toAjax($s) {
		return $this->toXML($this->toHTML($s));
	}

}

class HTMLPageRenderer extends PageRenderer {

}

class StandardPageRenderer extends HTMLPageRenderer {
	function initializeScripts(&$app) {
		$app->addStdRenderingSpecificScripts();
	}
	function renderJSCommands(&$window) {
		$xml = '';
		foreach (array_keys($window->ajaxCommands) as $i) {
			$xml .= $window->ajaxCommands[$i]->renderStdResponseCommand();
		}
		$a = array();
		$window->ajaxCommands =& $a;

		return $xml;
	}
	function renderPage(&$win){
		#@typecheck $win:Window@#
		$ret = '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

		$view =& $win->wholeView;
	//	$ret = '';
		$ret .= "<html>\n<head><title>" .$view->title .	"</title>";
		$ret .= $this->app->renderExtraHeaderContent();

		foreach ($this->app->style_sheets as $c) {
			$d = '';
			foreach($c as $k=>$v) {
				$d .= $k.'="'.$v.'" ';
			}
			$ret .= "\n<link type=\"text/css\" rel=\"stylesheet\" " . $d . " />";
		}


		$ret .= '</head><body>';
		if (Compiler::CompileOpt('debugview') || defined('debugview')){
			$ret .='<div>' .
						'<form action="'.site_url . 'Action.php" '.
							' method="post"'.
							' enctype="multipart/form-data">';
			if(isset($_REQUEST['app'])) {
				$ret .=		'<input type="hidden" name="app" value="'.$_REQUEST['app'].'" />';
			}
			$ret .=			'<input type="hidden" name="event_target" value="app" />' .
							'<input type="hidden" name="event" value="reset_templates" />' .
							'Debug View ' .
							'<input type="checkbox" checked="checked" onchange="document.getElementsByTagName(\'link\')[1].disabled = !document.getElementsByTagName(\'link\').item(1).disabled;"/>' .
							'<input type="submit" value="Reload Templates"/>' .
							'<a href="Action.php?restart=yes'.(isset($_REQUEST['app'])?'&app='.getClass($this->app):'').'">Restart application</a>' .
						'</form>'.
						'</div>';

		}
		foreach ($this->app->scripts as $s) {
			$ret .= "\n<script type=\"text/javascript\" src=\"" . $s . "\"></script>";
		}

		$ret .= "\n<script type=\"text/javascript\">";

		foreach ($this->app->jsscripts as $s) {
			  $ret .= $s;
		}
		$ret .= $this->renderJSCommands($win);
		$ret .= "</script>";

		$ret .= $win->wholeView->render();
		$ret .= '</body></html>';

		//$this->page->flushModifications();

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
		//$this->page->flushModifications();
		exit;
	}
}

class DebugPageRenderer extends StandardPageRenderer {
	function renderPage() {
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		echo $this->page->render();
		//$this->page->flushModifications();
		exit;
	}
}

class AjaxPageRenderer extends PageRenderer {
	function initPage(&$win){
		parent::initPage($win);
		$win->wholeView->setAttribute('onsubmit','refresh();');
	}
	function initialRender(&$win){
		$win->redraw();
	}
	function initializeScripts(&$app) {
		$app->addAjaxRenderingSpecificScripts();
	}
	function renderPage(&$win){
		#@typecheck $win:Window@#
		return $this->initialPageRenderPage($win);
	}
	function ajaxRenderPage(&$win){
		#@typecheck $win:Window@#
		header("Content-type: text/xml");
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		$xml .= "\n<ajax>";
		$xml .= $win->wholeView->renderAjaxResponseCommand();
		$xml .= $this->renderJSCommands($win);
		$xml .= "</ajax>";

		//$this->page->flushModifications();

		return $xml;
	}

	function renderJSCommands(&$window) {
		$xml = '';
		foreach (array_keys($window->ajaxCommands) as $i) {
			$xml .= $window->ajaxCommands[$i]->renderAjaxResponseCommand();
		}
		$a = array();
		$window->ajaxCommands =& $a;

		return $xml;
	}

	function toAjax($s) {
		return $this->toXML($this->toHTML($s));
	}

}

class DebugAjaxPageRenderer extends AjaxPageRenderer
{
	function renderActionLinkAction(& $action_link) {
		return 'callAction(&#34;' . $action_link->getId() . '&#34;);';
	}
}
?>
