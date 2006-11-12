<?php
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../lib/basiclib.php';
$htmls = getfilesrec(lambda('$file', '$v=substr($file, -4)==".xml";return $v;'),$_REQUEST['dir']);
print_r($htmls);
function convertToXul($html) {
	$convs = array(
		'table'=>'grid',
		'span'=>'label',
		'div'=>'box',
		'tbody'=>'rows',
		'tr'=>'row',
		'td'=>'box',
		'img'=>'image',
		'a'=>'box',
		'font'=>'box',
		'input'=>'textbox',
		);
	foreach($convs as $h=>$x) {
		$html = ereg_replace('(<|</)'.$h.'([ \t\n\r\f\v]*|>|/>)', '\1'.$x.'\2', $html);
/*		$html = ereg_replace('<'.$h.'>', '<'.$x.'>', $html);
		$html = ereg_replace('<'.$h.'/', '<'.$x.'/', $html);
		$html = ereg_replace('</'.$h.'\b', '</'.$x.' ', $html);
		$html = ereg_replace('</'.$h.'>', '</'.$x.'>', $html);*/
	}
	return $html;
}


foreach($htmls as $html) {
	$xul = substr($html,0,-3).'xul';
	if (!file_exists($xul) || isset($_REQUEST['recreate'])){
		echo "creating $xul;";
		$f = fopen($xul, 'w');
		fwrite($f, convertToXul(file_get_contents($html)));
		fclose($f);
	} else {
		echo "file $xul already exists;";
	}
}

?>
