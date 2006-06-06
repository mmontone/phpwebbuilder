<?php
require_once dirname(__FILE__) . '/Input.class.php';

class Filename extends Input {
	var $file;
	function initializeDefaultView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'file');
	}
	function setEvents(&$view){
		parent::setEvents(&$view);
		$view->setAttribute('onchange', 'uploadFile(&#34;'.$this->getId().'&#34;)');
	}
	function loadFile($file_data){
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
		$this->file =& $file;
	}
	function viewUpdated($params){
		$this->loadFile($params);
	}
}
?>