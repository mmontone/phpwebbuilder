<?php

class Filename extends Input {
	var $file;
	var $fileuploaded = false;
    var $file_holder;

    function Filename(&$text_holder, &$file_holder) {
        parent::Input($text_holder);
        $this->file_holder =& $file_holder;
    }

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
		$ex =& $file->save();
		$file->bin_data->setValue(null);
		$file->commitChanges();
		$db =& DBSession::instance();
		$db->clearLastSQL();
		if (is_exception($ex)) {
			return $ex;
		}
		$this->file =& $file;
        $this->file_holder->setValue($file);
        $this->fileuploaded=true;
		$this->triggerEvent('changed',$n=null);
		$this->viewHandler->initializeDefaultView($this->view);
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