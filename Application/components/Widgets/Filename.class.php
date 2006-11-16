<?php

class Filename extends Input {
	var $file;
	var $fileuploaded = false;
	function setEvents() {}

	function loadFile($file_data) {
		$file =& new File;
		if (!is_uploaded_file($file_data['tmp_name'])) {
			return new PWBException(array('message'=>'Error uploading File'));
		}
		$fields =& $file->allFields();
		$bin_data = addslashes(fread(fopen($file_data['tmp_name'], 'r'), max($file_data['size'],1)));
		unlink($file_data['tmp_name']);
		$file->bin_data->setValue($bin_data);
		$file->filename->setValue($file_data['name']);
		$file->filesize->setValue($file_data['size']);
		$file->filetype->setValue($file_data['type']);
		$this->file =& $file;
		$ex =& $file->save();
		$file->bin_data->setValue(null);
		$file->commitChanges();
		$db =& DBSession::instance();
		$db->clearLastSQL();
		if (is_exception($ex)) {
			return $ex;
		}
		$this->fileuploaded=true;
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