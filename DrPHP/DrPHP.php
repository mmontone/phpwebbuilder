<?php

$d = dirname(__FILE__);

compile_once (dirname(__FILE__).'/DrPHP.class.php');
compile_once (dirname(__FILE__).'/CodeAnalyzer.class.php');
compile_once (dirname(__FILE__).'/AnalisysCase.class.php');
compile_once (dirname(__FILE__).'/cases/NoCopies.class.php');
compile_once (dirname(__FILE__).'/cases/NoDoubleQuotes.class.php');
compile_once (dirname(__FILE__).'/cases/WrongGetters.class.php');

?>
