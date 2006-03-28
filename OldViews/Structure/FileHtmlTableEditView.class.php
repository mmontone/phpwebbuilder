<?php

require_once dirname(__FILE__) . '/HtmlTableEditView.class.php';

class FileHtmlTableEditView extends PersistentObjectHtmlTableEditView
{
    function show($linker){
        if ($_REQUEST['Action'] == 'Add')
    		return $this->showFields($linker, array('label', 'filename', 'description'));
        if ($_REQUEST['Action'] == 'Edit')
      		return $this->showFields($linker, array('label', 'description'));
        trigger_error('Error showing a FileHtmlTableEditView');
	}
}

?>
