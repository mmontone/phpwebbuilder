<?php


if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')){
	session_cache_limiter("public");
}

class ReadFile {
    var $windows = array();

    function ReadFile() {
        $this->windows['root'] =& new ReadFileWindow;
	}
    function preparePage(){}

}

class ReadFileWindow {
	function render() {
        $obj =& File::getWithId('File', $_REQUEST["fileid"]);
        header('Content-Type: '.$obj->filetype->getValue());
        header('Content-Length: '.$obj->filesize->getValue());
        header('Content-Disposition: attachment; filename="'.$obj->filename->getValue().'"');
        print $obj->bin_data->getCompleteValue();
        exit;
	}
}

class DisplayFile {
	var $windows = array();

    function DisplayFile() {
        $this->windows['root'] =& new DisplayFileWindow;
    }
    function preparePage(){}
}


class DisplayFileWindow {
    function render() {
        $obj =& File::getWithId('File', $_REQUEST["fileid"]);
        header('Content-Type: '.$obj->filetype->getValue());
        header('Content-Length: '.$obj->filesize->getValue());
        header('Content-Disposition: filename="'.$obj->filename->getValue().'"');
        print $obj->bin_data->getCompleteValue();
        exit;
    }
}

?>