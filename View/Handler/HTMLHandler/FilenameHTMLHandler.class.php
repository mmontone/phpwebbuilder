<?php

class FilenameHTMLHandler extends InputHTMLHandler{
	function initializeDefaultView(&$view) {
		if ($this->component->file===null) {
			$view->setTagName('iframe');
			$view->appendChild(new XMLTextNode('&nbsp;'));

			$view->setAttribute('src', toAjax(pwb_url.'/lib/uploadFile.php' .

									'?basedir='.basedir.'&filenamefield='.$this->component->getId()
									. '&app='.app_class. '&pwb_config='.$_REQUEST['pwb_config']));
			$view->addCSSClass('uploadfile');
			$w =& Window::getActiveInstance();
			$w->closeStream=true;
		} else {
			$view->setTagName('span');
			$view->removeChilds();
			$view->appendChild(new XMLTextNode($this->component->file->filename->getValue()));
		}
	}
}
?>