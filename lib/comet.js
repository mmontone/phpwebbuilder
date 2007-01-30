
function nothing(){}
var cometurl;
function enableComet(){
	ifr = document.getElementById('comet');
	if (cometurl==null) {enqueueUpdate(new Update("ajax", "true"));cometurl = 'Action.php?'+createSubmitData();}
	if (ifr==null){
		var a = document.getElementById('app');
		var ifr=document.createElement('iframe');
		ifr.setAttribute('id','comet');
		ifr.style.visibility='hidden';
		ifr.style.width='0';
		ifr.style.height='0';
		a.appendChild(ifr);
		ifr.src=cometurl;
		keepalive(10000,20);
	}
}

function closeComet(){
	com = document.getElementById('comet');
	com.parentNode.removeChild(com);
	loadingStop(cometCount=0);
}

function keepalive(time,count){
	if (count==0) return;
    f = function (){
	   postToAjax(createSubmitData(),document.getElementById("pwb_url").getAttribute("value")+'lib/comet.php',nothing,false);
       keepalive(time,count-1);
    }
    setTimeout("f()",time);
}

addOnLoad(enableComet);
addOnUnload(closeComet);
cometCount=0;
function refresh(){
    enableComet();
    loadingStart(++cometCount);
    enqueueUpdate(new Update("showStopLoading", "true"));
	postToAjax(createSubmitData(),document.getElementById("pwb_url").getAttribute("value")+'lib/comet.php',nothing,false);
}