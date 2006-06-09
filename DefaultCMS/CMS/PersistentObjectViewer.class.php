<?php

require_once 'PersistentObjectPresenter.class.php';

class PersistentObjectViewer extends PersistentObjectPresenter {
    function initialize(){
    	$this->factory =& new ViewerFactory;
		parent::initialize();
    }
}
?>