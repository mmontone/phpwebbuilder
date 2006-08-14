<?php

class Filename extends Input {
	var $file;
	var $fileuploaded = false;

	function initializeDefaultView(&$view) {
		$view->setTagName('iframe');
		$view->setAttribute('src', pwb_url.'/lib/uploadFile.php' .
								'?basedir='.basedir.'&filenamefield='.$this->getId()
								. '&app='.app_class);
		$view->addCSSClass('uploadfile');
	}

	function setEvents(&$view) {
		parent::setEvents(&$view);
		$view->removeAttribute('onchange');
	}

	function loadFile($file_data) {
		$file =& new File;
		if (!is_uploaded_file($file_data['tmp_name'])) {
			return false;
		}
		$fields =& $file->allFields();
		$bin_data = addslashes(fread(fopen($file_data['tmp_name'], 'r'), max($file_data['size'],1)));
		unlink($file_data['tmp_name']);
		$fields['bin_data']->setValue($bin_data);
		unset($bin_data);
		$fields['filename']->setValue($file_data['name']);
		$fields['filesize']->setValue($file_data['size']);
		$fields['filetype']->setValue($file_data['type']);
		$this->fileuploaded=true;
		$this->file =& $file;
		return true;
	}

	function &getFile() {
		return $this->file;
	}

	function isFileLoaded() {
		return $this->fileuploaded;
	}

	function viewUpdated($params) {
		$this->loadFile($params);
	}
}
?>