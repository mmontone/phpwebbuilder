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

		/* Now we need to save the file in the DB. For that we cannot use the current DBSession  instance, as
		 * the INSERT query would remain as a delayed query for the long transaction. To avoid that, we create
		 * a new DBSession instance and save the file with it. We access the DB "from the outside".
		 *            -- marian
		 */
		$dbsession_class = 'DBSession';
		if (defined('dbsession_class')) {
			$dbsession_class = constant('dbsession_class');
		}
		$driver_class = constant('db_driver');
		$dbsession = & new $dbsession_class;
		$dbsession->driver = & new $driver_class ($dbsession);
		$dbsession->beginTransaction();
		$ex =& $dbsession->save($file);
		if (is_exception($ex)) {
			return $ex;
		}
		$dbsession->commitTransaction();

		$file->bin_data->setValue(null);
		$file->commitChanges();
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