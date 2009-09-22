<?php
define('app_class',$_REQUEST["app"]);
ob_start();
require_once $_REQUEST["basedir"]. '/Configuration/pwbapp.php';
$app = & Application :: instance();
$ad =& new  ActionDispatcher;
//var_dump($_REQUEST["component"]);
$comp =& $ad->getComponent($_REQUEST["component"], $app);
//var_dump($comp);
$options =& $comp->getElements($_REQUEST["value"]);
$elems =& $options->elements();
ob_flush();
echo "<ul>";
$i=0;
foreach($elems as $elem){
    echo "<li value='".$i++."'>".$elem->printString()."</li>";
}
echo "</ul>";
ob_start();
?>
