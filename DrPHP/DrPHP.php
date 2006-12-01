<?php

$d = dirname(__FILE__);

compile_once ($d.'/DrPHP.class.php');
compile_once ($d.'/CodeAnalyzer.class.php');
compile_once ($d.'/AnalisysCase.class.php');
compile_once ($d.'/cases/NoCopies.class.php');
compile_once ($d.'/cases/NoDoubleQuotes.class.php');
compile_once ($d.'/cases/WrongGetters.class.php');

?>
