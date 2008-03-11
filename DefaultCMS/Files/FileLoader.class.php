<?php

class FileLoader extends Component{
	var $label_text;
	var $desc_text;

	function FileLoader(&$file_field) {
		parent::Component();
		$this->label_text =& new ValueHolder('');
		$this->desc_text =& new ValueHolder('');
		$this->fileField =& $file_field;
	}

	function permissionNeeded () {
		return "DatabaseAdmin";
	}

    function initialize() {
		$this->addLabelInput();
		$this->addFilenameField();
		$this->addDescriptionInput();
		$this->addStatusDisplayer();
		$this->addUploadButton();
		$this->addCancelButton();
    }

    function addCancelButton() {
    	$this->addComponent(new CommandLink(array('text' => Translator::Translate('cancel'), 'proceedFunction' => new FunctionObject($this, 'cancel'))),'cancel');
    }

    function addUploadButton() {
    	$this->addComponent(new CommandLink(array('text' => Translator::Translate('save'), 'proceedFunction' => new FunctionObject($this, 'save'))),'save');
    }

    function addLabelInput() {
    	$this->addComponent(new Input($this->label_text),'label');
    }

    function addDescriptionInput() {
    	$this->addComponent(new TextAreaComponent($this->desc_text),'description');
    }

    function addStatusDisplayer() {
    	$this->addComponent(new Text($this->status_text),'status');
    }

    function save(){
		if (!$this->filename->isFileLoaded()) {
			$this->uploadFailed();
			$this->addFilenameField();
			return false;
		}
		else {
			$file =& $this->filename->file;
			if ($ex =& $this->checkFile($file)) {
				$this->uploadFailed($ex->getMessage());
				$this->addFilenameField();
				return false;
			}
			else {
				$file->label->setValue($this->label_text->getValue());
				$file->description->setValue($this->desc_text->getValue());

				if ($this->saveFile($file)) {
					$this->uploadSuccessful();
				} else {
					$this->uploadFailed();
				}
			}
		}
    }

    function saveFile(&$file) {
		return $file->save();
    }

    function uploadSuccessful() {
    	$this->status_text->setValue($v = Translator::Translate('Upload Successful'));
    }

    function uploadFailed($msg = 'Upload failed') {
    	$this->status_text->setValue($v = Translator::Translate($msg));
		$this->addFilenameField();
    }

    function &checkFile(&$file) {
    	return false;
    }

    function addFilenameField() {
    	echo 'addFilenameField';
    	$this->addComponent(new Filename(new ValueHolder($f=''), $this->fileField),'filename');
    }

    function cancel() {
		$this->callback('cancel');
    }
}

?>
