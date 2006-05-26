<html>
<head>
<script>
f = function (e){
	document.getElementById("pipi").value="bybye";
	print_s("test");
	for(i in e){
		print_s(i+" = "+e[i]+"lala");
	}
//	window.location =window.location;
	print_s(exited);
};
window.onload=function (){print_s("loaded");}
window.onunload=f;
function print_s(s){
	document.getElementsByTagName("body")[0].appendChild(document.createTextNode(s));
}
</script>
</head>
<body>
<input type="text" id="pipi" value="algo"/>
</body>
</html>