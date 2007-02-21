
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
		/*ifr.style.width='100%';
		ifr.style.height='300px';*/
		a.appendChild(ifr);
		return false;
	} else {
		return true;
	}
	//keepalive(20000,5);
}

function closeComet(){
	com = document.getElementById('comet');
	com.parentNode.removeChild(com);
	//alert('closing');
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
