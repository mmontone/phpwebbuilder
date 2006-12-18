<?php


if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')){
	session_cache_limiter("public");
}


class ReadFile {
	function render(){
		$obj =& File::getWithId('File', $_REQUEST["fileid"]);
		header('Content-Type: '.$obj->filetype->getValue());
		header('Content-Length: '.$obj->filesize->getValue());
		header('Content-Disposition: attachment; filename="'.$obj->filename->getValue().'"');
		print $obj->bin_data->getCompleteValue();
	}
}
?>