<?php

	$gestor=opendir(dirname(__FILE__));
	while (false !== ($f = readdir($gestor))) {
		if (substr($f,-1)!='.' && $f!='test.php') {
			$fs .= '<frame src="'.dirname($_SERVER["REQUEST_URI"]) .'/'. $f;
			$fs .= '"/>';
			$rs[]="1*";
		}
	}
	$rows = implode(',', $rs);

?>
<frameset border="1" ROWS="<?=$rows?>">
<?=$fs?>
</frameset>