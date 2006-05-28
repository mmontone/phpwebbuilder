<?
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
remf = document.getElementById("fileelem");
document.getElementById("nodeid").setAttribute('value','<?=$_REQUEST["filenamefield"]?>');
doc = window.frameElement.ownerDocument;
movf = doc.getElementById('<?=$_REQUEST["filenamefield"]?>');
document.getElementById("basedir").setAttribute('value', doc.getElementById("basedir").getAttribute('value'));
newf = document.createElement('span');
for(var i in movf.attributes){
	att = movf.attributes[i].nodeName;
	newf.setAttribute(att, movf.getAttribute(att));
}
newf.appendChild(document.createTextNode("uploading "+movf.value+"..."));
movf.parentNode.replaceChild(newf, movf);
remf.parentNode.replaceChild(movf, remf);
movf.setAttribute('id', 'fileelem');
movf.setAttribute('name', 'fileelem');
window.onload=function (){document.getElementById('fm').submit();}
</script>
<? } else {
	$path = split("/", $_REQUEST["nodeid"]);
	$appclass = $path[1];
	$app = & Application :: getInstanceOf($appclass);
	$comp =& $_SESSION['action_dispatcher']->getComponent($_REQUEST["nodeid"], $app);
	$comp->loadFile($_FILES["fileelem"]);
?>
<script>
ifr = window.frameElement;
doc = ifr.ownerDocument;
text = doc.getElementById('<?=$_REQUEST["nodeid"]?>');
text.replaceChild(document.createTextNode("uploaded <?=$_FILES["fileelem"]["name"]?>"), text.firstChild);
ifr.parentNode.removeChild(ifr);
</script>
<?}?>

</body>
</html>