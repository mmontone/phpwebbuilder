
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
		a.appendChild(ifr);
		ifr.src=cometurl;
	}
}

function closeComet(){
	com = document.getElementById('comet');
	com.parentNode.removeChild(com);
}

addOnLoad(enableComet);
addOnUnload(closeComet);

function refresh(){
    enableComet();
    enqueueUpdate(new Update("comet", "true"));
	postToAjax(createSubmitData(),document.getElementById("app").getAttribute("action"),nothing);
}