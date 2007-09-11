<?php

require_once('../Application/ActionDispatcher.class.php');

if (ActionDispatcher::SendData($_REQUEST)){
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
echo '<ajax></ajax>';
} else {
	echo 'error, could not send'.$ec;
}

?>
