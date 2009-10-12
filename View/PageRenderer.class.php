<?php

#@preprocessor
if (isset($_REQUEST['render'])) {
			$page_renderer= $_REQUEST['render'].'PageRenderer';
		} else if (defined('page_renderer')){
			$page_renderer = constant('page_renderer');
		} else {
			$page_renderer = 'StandardPageRenderer';
		}
Compiler::usesClass(__FILE__,$page_renderer);
//@#

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
	function  rendersAjax(){
		return false;
	}
	function &create(&$app){
		$v = null;
		$page_renderer = PageRenderer::getRendererClass();
		$v =& new $page_renderer($app);
		return $v;
	}
	function getRendererClass(){
		if (isset($_REQUEST['render'])) {
			$page_renderer= $_REQUEST['render'].'PageRenderer';
		} else if (defined('page_renderer')){
			$page_renderer = constant('page_renderer');
		} else {
			$page_renderer = 'StandardPageRenderer';
		}
		return 	$page_renderer;
	}
	function sendRestart(){
		$page_renderer = PageRenderer::getRendererClass();
		eval("$page_renderer::basicSendRestart();");
	}
	function basicSendRestart(){}
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
		$this->addVariable($view,'pwb_config', @$_REQUEST['pwb_config']);
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
	function setTitle(&$win, $title){
		$win->wholeView->title=toAjax($title);
	}
	function initialRender(){}
	function render(&$win){
		#@typecheck $win:Window@#
		if (isset($_REQUEST['ajax'])){
			$this->ajaxRenderPage($win);
		} else if (isset($_REQUEST['comet'])){
			$this->cometRenderPage($win);
		} else	{
			$this->renderPage($win);
		}
	}
	function preparePage() {
		if (isset($_REQUEST['ajax'])){
			$this->ajaxPreparePage();
		} else if (isset($_REQUEST['comet'])){
			$this->cometPreparePage();
		} else	{
			$this->basicPreparePage();
		}
		pwb_register_shutdown_function('closePage', new FunctionObject($this, 'closePage'));
	}
	function basicPreparePage(){}

	function closePage() {
		if (isset($_REQUEST['ajax'])){
			$this->ajaxClosePage();
		} else if (isset($_REQUEST['comet'])){
			$this->cometClosePage();
		} else	{
			$this->basicClosePage();
		}
	}
	function basicClosePage(){}


	function ajaxRenderPage(&$win){
		#@typecheck $win:Window@#
		$initial_page_renderer = & new AjaxPageRenderer(Application::Instance());
		$initial_page_renderer->ajaxRenderPage($win);
	}
	function cometRenderPage(&$win){
		#@typecheck $win:Window@#
		$initial_page_renderer = & new CometPageRenderer(Application::Instance());
		$initial_page_renderer->cometRenderPage($win);
	}
	function templateExtension(){
		return '.xml';
	}
	function defaultTag(){
		return 'div';
	}
	function &viewHandler(){
		$h =& new HTMLHandler;
		return $h;
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

	function toHTML2($s) {
		$s = htmlentities($s);
		return $s;
	}

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
		$s = str_replace('&acute;', '&#39;', $s);
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
	function releaseSession(){}
}

class HTMLPageRenderer extends PageRenderer {}

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
	var $cached;
	function renderPage(&$win){
		#@typecheck $win:Window@#
		header('Content-Type: text/html; charset=UTF-8');
		if ($this->cached==null) {
			$ret = '<!DOCTYPE html
			     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
			     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

			$view =& $win->wholeView;
			$ret .= "<html>\n<head>".

					"<title>" .$view->title .	"</title>";
			$ret .= $this->app->renderExtraHeaderContent();

			foreach ($this->app->style_sheets as $c) {
				$d = '';
				foreach($c as $k=>$v) {
					$d .= $k.'="'.$v.'" ';
				}
				$ret .= "\n<link type=\"text/css\" rel=\"stylesheet\" " . $d . " />";
			}
			$ret .='<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">'.
				   '<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">';


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
								'<input type="button" value="View Source" onclick="reconstructTemplates()"/>' .
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
			$ret .= "</script>";
			$this->cached = $ret;
		}
		echo $this->cached;
		echo "\n<script type=\"text/javascript\">Event.observe(window, 'load', function(){";
		echo $this->renderJSCommands($win);
		echo "});</script>";
		echo $win->wholeView->render();
		echo '</body></html>';
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

class AjaxPageRenderer extends PageRenderer {
	function setTitle(&$win, $title){
		$win->wholeView->title=toAjax($title);
		$win->addAjaxCommand(new AjaxCommand('document.title=',array($title)));
	}
	function initPage(&$win){
		parent::initPage($win);
		$win->wholeView->setAttribute('onsubmit','refresh();');
		$win->wholeView->setAttribute('onkeyup', 'dataChanged(event)');
	}
	function  rendersAjax(){
		return true;
	}
	function initialRender(&$win){
		$win->wholeView->registering=true;
		$win->redraw();
	}
	function initializeScripts(&$app) {
		$app->addAjaxRenderingSpecificScripts();
	}
	function renderPage(&$win){
		#@typecheck $win:Window@#
		return $this->initialPageRenderPage($win);
	}
	function ajaxPreparePage(){
		header("Content-type: text/xml; charset=UTF-8");
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<ajax>';
		echo '<output><![CDATA[';
	}

	function ajaxRenderPage(&$win){
		#@typecheck $win:Window@#
		echo ']]></output>';
		echo $win->wholeView->renderAjaxResponseCommand();
		echo $this->renderJSCommands($win);
		echo '<output><![CDATA[';
	}
	function ajaxClosePage(){
		echo ']]></output>';
		echo '</ajax>';
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
		if (isset($_REQUEST['ajax'])){
			return $this->toXML($this->toHTML($s));
		} else {
			return parent::toAjax($s);
		}
	}
}

?>