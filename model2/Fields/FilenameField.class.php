<?php

require_once dirname(__FILE__) . '/TextField.class.php';

class FilenameField extends TextField {
	function &visit(&$obj) {
		return $obj->visitedFilenameField($this);
	}

      function FilenameField ($name, $isIndex) {
               parent::TextField($name, $isIndex);
      }
}


?>