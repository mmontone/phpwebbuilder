<?php

class XULPageRenderer extends PageRenderer {
	function initializeScripts(&$app) {
		$app->addStdRenderingSpecificScripts();
	}

	function renderPage(&$app) {

		$ret = '<?xml version="1.0"?>
		<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>
		<window
    id="findfile-window"
    title="Hello worls"
    orient="horizontal"

    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">';

//xmlns:html="http://www.w3.org/1999/xhtml"

		/*$ret .= "<html>\n<head><title>" .$this->page->title .	"</title>";
		$ret .= $app->commonCSS();
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
		/*$ret .= '</body></html>';*/
		$ret .= '<button
    id="identifier"
    class="dialog"
    label="OK"
    image="images/image.jpg"
    disabled="false"
    accesskey="t"/>
<label control="some-text" value="Enter some text"/>
<textbox id="some-text"/>
<label control="some-password" value="Enter a password"/>
<textbox id="some-password" type="password" maxlength="8"/>
<listbox>
  <listitem label="Butter Pecan"/>
  <listitem label="Chocolate Chip"/>
  <listitem label="Raspberry Ripple"/>
  <listitem label="Squash Swirl"/>
</listbox>
';
		$ret .= '</window>';
		$this->page->flushModifications();

		return $ret;
	}

}
?>
