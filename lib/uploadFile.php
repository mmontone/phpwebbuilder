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
<script type="text/javascript">
function submitting(){
var parwin = window.frameElement.ownerDocument.window;
parwin.start_uploading("<?=$_REQUEST["filenamefield"]?>", document.getElementById('fileelem').value);
document.getElementById('fm').submit();
}
</script>
<style type="text/css">
* {
padding:0;
margin:0;
}
</style>

<form id="fm" action="uploadFile.php" method="post" enctype="multipart/form-data">
<input type="file" id="fileelem" name="fileelem" onchange="submitting()"/>
<input type="hidden" name="nodeid" id="nodeid" value="<?=$_REQUEST['filenamefield']?>"/>
<input type="hidden" name="app" id="app" value="<?=$_REQUEST['app']?>"/>
<input type="hidden" name="pwb_config" id="pwb_config" value="<?=$_REQUEST['pwb_config']?>"/>
<input type="hidden" name="basedir" id="basedir" value="<?=$_REQUEST['basedir']?>"/>
</form>
<? } else {
	$app = & Application :: instance();
	$ad =& new  ActionDispatcher;
	$comp =& $ad->getComponent($_REQUEST["nodeid"], $app);
	if(!is_exception($ex =&$comp->loadFile($_FILES["fileelem"]))){

?>
<script>
var parwin = window.frameElement.ownerDocument.window;
parwin.end_uploading("<?=$_REQUEST["nodeid"]?>", "<?=$_FILES["fileelem"]["name"]?>");
</script>
<?} else {?>
<script>
var parwin = window.frameElement.ownerDocument.window;
parwin.error_uploading("<?=$_REQUEST["nodeid"]?>", "<?=$ex->getMessage()?>");
</script><?}
}?></body>
</html>
