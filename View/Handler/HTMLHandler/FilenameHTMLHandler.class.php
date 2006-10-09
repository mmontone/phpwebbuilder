<?php

class FilenameHTMLHandler extends InputHTMLHandler{
	function initializeDefaultView(&$view) {
		$view->setTagName('iframe');
		$view->appendChild(new XMLTextNode('&nbsp;'));

		$view->setAttribute('src', toAjax(pwb_url.'/lib/uploadFile.php' .

								'?basedir='.basedir.'&filenamefield='.$this->component->getId()
								. '&app='.app_class));
		$view->addCSSClass('uploadfile');
	}
}
?>