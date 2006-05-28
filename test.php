<html>
<head>
<script>
function print_s(s){
	document.getElementsByTagName("body")[0].appendChild(document.createTextNode(s));
}
f = function (){
print_s(window.frames.length);
}
window.onload=f;
</script>
</head>
<frameset cols="100%,0%">
<frame name="main" />
<frame name="hidden" />
<frameset>
</html>