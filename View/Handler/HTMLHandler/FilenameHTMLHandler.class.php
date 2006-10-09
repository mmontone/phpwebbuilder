<?php

class FilenameHTMLHandler extends InputHTMLHandler{
	function initializeDefaultView(&$view) {
		$view->setTagName('iframe');
		$view->setAttribute('src', toAjax(pwb_url.'/lib/uploadFile.php' .
								'?basedir='.basedir.'&filenamefield='.$this->getId()
								. '&app='.app_class));
		$view->addCSSClass('uploadfile');
	}
}
?>