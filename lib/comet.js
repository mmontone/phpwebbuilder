
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
	if (cometError){
		ifr.parentNode.removeChild(ifr);
		ifr=null;
	}
	if (ifr==null){
		cometError = false;
		cometInterrupted = true;
		var a = document.getElementById('app');
		var ifr=document.createElement('iframe');
		ifr.setAttribute('src',getCometUrl());
		ifr.setAttribute('id','comet');
		ifr.style.display='none';
		a.appendChild(ifr);
		return false;
	} else {
		return true;
	}
	//keepalive(20000,5);
}

var cometError = false;
var cometInterrupted = false;

function closeComet(){
	ifr = document.getElementById('comet');
	if (cometInterrupted){
		cometError = true;
		ajaxError();
		ifr.style.display='block';
	} else {
		ifr.parentNode.removeChild(ifr);
	}
	loadingStop(cometCount=0);
}

keepalivetimes = 0;
keepalivef = function (){
	   if (keepalivetimes==0) {
		  clearInterval(keepalivefid);
	   } else {
	      keepalivetimes--;
	      postToAjax(windowurl,document.getElementById("pwb_url").getAttribute("value")+'lib/comet.php',nothing,false);
       }
    };
;
keepalivefid=null;

function keepalive(time,count){
	if (keepalivetimes == 0) {
		keepalivefid= setInterval("keepalivef()",time);
	}
	keepalivetimes = count;
}

addOnLoad(function () {window.enableComet();});
cometCount=0;
cometStarted=function (){};
function refresh(){
    loadingStart(++cometCount);
    cometStarted = function (){
	    enqueueUpdate(new Update("showStopLoading", "true"));
		postToAjax(createSubmitData(),document.getElementById("pwb_url").getAttribute("value")+'lib/comet.php',nothing,false);
	};
    if (enableComet()){
		cometStarted();
	}
}
