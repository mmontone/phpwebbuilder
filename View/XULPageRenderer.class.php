<?php

class XULPageRenderer extends PageRenderer {
	function initializeScripts(&$app) {
		$app->addStdRenderingSpecificScripts();
	}

	function renderPage(&$app) {
		header("Content-type: text/xml");
		$ret = '<?xml version="1.0"?>
		<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>
		<window
    id="findfile-window"
    title="Hello worls"
    orient="horizontal"
	xmlns="http://www.w3.org/1999/xhtml"
    xmlns:xul="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">';



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
		$ret .= '<xul:button
    id="identifier"
    class="dialog"
    label="OK"
    image="images/image.jpg"
    disabled="false"
    accesskey="t"/>
<xul:label control="some-text" value="Enter some text"/>
<xul:textbox id="some-text"/>
<xul:label control="some-password" value="Enter a password"/>
<xul:textbox id="some-password" type="password" maxlength="8"/>
<xul:listbox>
  <xul:listitem label="Butter Pecan"/>
  <xul:listitem label="Chocolate Chip"/>
  <xul:listitem label="Raspberry Ripple"/>
  <xul:listitem label="Squash Swirl"/>
</xul:listbox>
';
		$ret .= '</window>';
		$this->page->flushModifications();

		return $ret;
	}

}
?>