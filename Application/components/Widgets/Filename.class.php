<?php

class Filename extends Input {
	var $file;
	var $fileuploaded = false;
	function setEvents() {}

	function loadFile($file_data) {
		$file =& new File;
		if (!is_uploaded_file($file_data['tmp_name'])) {
			return false;
		}
		$fields =& $file->allFields();
		$bin_data = addslashes(fread(fopen($file_data['tmp_name'], 'r'), max($file_data['size'],1)));
		unlink($file_data['tmp_name']);
		$file->bin_data->setValue($bin_data);
		unset($bin_data);
		$file->filename->setValue($file_data['name']);
		$file->filesize->setValue($file_data['size']);
		$file->filetype->setValue($file_data['type']);
		$this->fileuploaded=true;
		$this->file =& $file;
		$file->save();
		$file->bin_data->setValue(null);
		$file->commitChanges();
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