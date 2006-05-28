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
		$file =& $this->filename->file;
		$file->label->setValue($this->label->getValue());
		$file->description->setValue($this->description->getValue());
		$ok = $file->save();
		if ($ok) {
			$this->status->setValue('Upload Successful');
		} else {
			$this->status->setValue('Upload Failed');
		}
    }
}
?>