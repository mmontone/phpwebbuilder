
function nothing(text,xml){}
var cometurl;var windowurl;
function getCometUrl(){
	if (cometurl==null) {
		windowurl='Action.php?'+createSubmitData();
		cometurl = windowurl+'&comet=true';
	}
	return cometurl;
}
function enableComet(){
	ifr = document.getElementById('comet');
	if (ifr==null){
		var a = document.getElementById('app');
		var ifr=document.createElement('iframe');
		ifr.setAttribute('src',getCometUrl());
		ifr.setAttribute('id','comet');
		ifr.style.visibility='hidden';
		ifr.style.width='0';
		ifr.style.height='0';
		a.appendChild(ifr);
	}
	keepalive(10000,20);
}

function closeComet(){
	com = document.getElementById('comet');
	com.parentNode.removeChild(com);
	loadingStop(cometCount=0);
}

keepalivetimes = 0;
keepalivef = null;


function keepalive(time,count){
	if (keepalivetimes == 0) {
		keepalivef= setInterval("f()",time);
	}
	keepalivetimes = count;
    f = function (){
	   if (keepalivetimes==0) {
		  clearInterval(keepalivef);
	   } else {
	      postToAjax(windowurl,document.getElementById("pwb_url").getAttribute("value")+'lib/comet.php',nothing,false);
	      keepalivetimes--;
       }
    }
}

addOnLoad(function () {window.enableComet();});

cometCount=0;
function refresh(){
    enableComet();
    loadingStart(++cometCount);
    enqueueUpdate(new Update("showStopLoading", "true"));
	postToAjax(createSubmitData(),document.getElementById("pwb_url").getAttribute("value")+'lib/comet.php',nothing,false);
}