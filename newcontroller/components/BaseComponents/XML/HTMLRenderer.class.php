<?php

require_once dirname(__FILE__) . '/XMLNode.class.php';

class HTMLRendererNew extends XMLNode{
	function renderPage(){
		$this->tagName='form';
		$this->setAttribute('action',"new_dispatch.php");
		$this->setAttribute('method',"POST");
		$this->setAttribute('enctype',"multipart/form-data");
		$ret="<html>\n" .
				"   <head><script src=\"".site_url."/admin/ajax/ajax.js\"></script></head><body>";
		$ret .= str_replace("\n", "\n   ", $this->render());
		$ret .="\n</body></html>";
		return $ret; 
	}
	function showXML(){
		$ret = $this->renderPage();
		$ret = str_replace("<", "&lt;", $ret );
		$ret = str_replace(">", "&gt;", $ret);
		$ret = str_replace("\n", "<br/>", $ret );
		$ret = str_replace("   ", "&nbsp;&nbsp;&nbsp;", $ret );
		return $ret;
	}
	
}

?>