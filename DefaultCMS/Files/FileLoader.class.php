<?php

class FileLoader extends Component{

    function initialize() {
		$this->addComponent(new Input(new ValueHolder($lab='')),'label');
		$this->addComponent(new Filename(new ValueHolder($f='')),'filename');
		$this->addComponent(new TextAreaComponent(new ValueHolder($des='')),'description');
		$this->addComponent(new Text(new ValueHolder($t='')),'status');
		$this->addComponent(new ActionLink($this, 'save', 'Save', $n=null),'save');
		$this->addComponent(new ActionLink($this, 'callback', 'Cancel', $n=null),'cancel');
    }
    function save(){
		$file =& new File;
		$file->label->setValue($this->label->getValue());
		$file->description->setValue($this->description->getValue());
		$this->saveFile($file);
    }
    function saveFile(&$file){
		$fileid = $this->filename->getId();
		$this->status->setValue(print_r($_FILES, TRUE));
		$file_data  =& $_FILES[$fileid];
		if (!is_uploaded_file($file_data ['tmp_name'])) {
			$this->status->setValue('Could not upload the file');
			return false;
		}
		$class = strtolower($this->obj->table);
		$fields =& $file->allFields();
		$bin_data = addslashes(fread(fopen($file_data["tmp_name"], "r"), $file_data["size"]));
		unlink($file_data["tmp_name"]);
		$fields['bin_data']->setValue($bin_data);;
		unset($bin_data);
		$fields['filename']->setValue($file_data['name']);
		$fields['filesize']->setValue($file_data['size']);
		$fields['filetype']->setValue($file_data['type']);
		$ok = $file->save();
		if ($ok) {
			$this->status->setValue('Upload Successful');
		} else {
			$this->status->setValue('Upload Failed');
		}
		return true;
	}
}
?>