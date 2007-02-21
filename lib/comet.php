<?php

session_name(strtolower($_REQUEST['app']));
$file = ini_get('session.save_path').'/'.$_REQUEST['app'].'-'.$_REQUEST[$_REQUEST['app']].'.cmt';
touch($file);
if (msg_send(msg_get_queue(ftok($file, 'c')),
	1,$_REQUEST,true,FALSE,$ec)) {
//$f = fopen($file, 'a+');
//flock($f,LOCK_EX);
//$str = fputs($f,'<newinput>'.serialize($_REQUEST));
//fclose($f);
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
echo '<ajax></ajax>';
} else {
	echo 'error, could not send'.$ec;
}

?>
