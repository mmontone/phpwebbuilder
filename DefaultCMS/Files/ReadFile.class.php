<?php

class ReadFile {
	function render(){
		$obj =& File::getWithId('File', $_REQUEST["fileid"]);
		header('Content-Type: '.$obj->filetype->getValue());
		header('Content-Disposition: attachment; filename="'.$obj->filename->getValue().'"');
		print $obj->bin_data->getValue();
	}
}
?>
