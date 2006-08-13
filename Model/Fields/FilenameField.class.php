<?php

class FilenameField extends TextField {
	function &visit(&$obj) {
		return $obj->visitedFilenameField($this);
	}
}


?>
