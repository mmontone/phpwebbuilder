<?php

require_once("Controller.class.php");

/**
 * Controller for editing the site's configuration
 */
class ConfigController extends Controller {
	var $roleid = 1;
	function permissionNeeded () {
		return "*";
	}
	function begin($form) {
		$ret ="";
		$ret .=$this->saveValues($form);
		$ret .=$this->showOptions($form);
		return $ret;
	}
	function saveValues($form) {
		if (isset($form["execconfig"])) {
			$from = basedir."MyConfig/Static_conf.php-".$form["configname"];
			$to = basedir."MyConfig/Static_conf.php";
			echo "copying from $from to $to";
			if (file_exists($from)) {
			$f = fopen($to, 'w');
			$str = file_get_contents($to);
			fwrite($f, $str);
			fclose($f);
			} else {
				echo "<br/>$from not found";
			}
			
		}
	}
	function showOptions($form) {
		$form = "<input type=\"text\" name=\"configname\" />";
		return $this->myForm("config",$form);
	}
}

?>
