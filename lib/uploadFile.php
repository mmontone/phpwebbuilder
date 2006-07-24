<?
define('app_class',$_REQUEST["app"]);
require_once $_REQUEST["basedir"]. '/Configuration/pwbapp.php';
?>
<html>
<head>
</head>
<body>
<?
if (isset($_REQUEST["filenamefield"])){
?>
<form id="fm" action="uploadFile.php" method="post" enctype="multipart/form-data">
<input type="file" id="fileelem"/>
<input type="hidden" name="nodeid" id="nodeid" value=""/>
<input type="hidden" name="basedir" id="basedir" value=""/>
<input type="button"  />
</form>
<script type="text/javascript">
var remf = document.getElementById("fileelem");
document.getElementById("nodeid").setAttribute('value','<?=$_REQUEST["filenamefield"]?>');
var doc = window.frameElement.ownerDocument;
var parwin = doc.window;
var movf = doc.getElementById('<?=$_REQUEST["filenamefield"]?>');
document.getElementById("basedir").setAttribute('value', doc.getElementById("basedir").getAttribute('value'));
parwin.start_uploading('<?=$_REQUEST["filenamefield"]?>');
remf.parentNode.replaceChild(movf, remf);
movf.setAttribute('id', 'fileelem');
movf.setAttribute('name', 'fileelem');
window.onload=function (){document.getElementById('fm').submit();}
</script>
<? } else {
	$app = & Application :: instance();
	$ad =& new  ActionDispatcher;
	$comp =& $ad->getComponent($_REQUEST["nodeid"], $app);
	$comp->loadFile($_FILES["fileelem"]);
?>
<script>
var ifr = window.frameElement;
var doc = ifr.ownerDocument;
var parwin = doc.window;
parwin.end_uploading('<?=$_REQUEST["nodeid"]?>', '<?=$_FILES["fileelem"]["name"]?>');
ifr.parentNode.removeChild(ifr);
</script>
<?}?>

</body>
</html>
