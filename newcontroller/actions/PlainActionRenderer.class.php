<?php

class PlainActionRenderer
{

    function PlainActionRenderer() {

    }

    function getEditActionLink(&$action) {
    	return $action->href();
    }

    function getAddActionLink(&$action) {
    	return $action->href();
    }

    function getSaveActionLink(&$action) {
      return $action->href();
    }

    function getCancelActionLink(&$action) {
      return $action->href();
    }
}
?>