
function initialize_history(){
    dhtmlHistory.initialize();
    this.fireOnNewListener = false;
    dhtmlHistory.addListener(historyChange);
    if (dhtmlHistory.isFirstLoad() || dhtmlHistory.getCurrentLocation() == "") {
	    var bm = document.getElementById("bookmark").getAttribute("value");
	    if (bm!='Home'){
        	dhtmlHistory.add(bm);
        }
    }
 }

function historyChange(newLocation, historyData) {
	if (dhtmlHistory.getCurrentLocation() == ""){
		newLocation='Home';
	}
    var bm = document.getElementById("bookmark").getAttribute("value");
    if (bm!='Home'){
        dhtmlHistory.add(bm);
    }
    sendUpdate(new Update("bm", newLocation));
}


addOnLoad(initialize_history);

function do_bookmark(bm){
    if (bm!='Home'){
        dhtmlHistory.add(bm);
    }
}

function ie_ajax_bookmark(action) {
    return ajax_bookmark(action);
}

function load_bookmark(hash){
    dhtmlHistory.add(hash, null);
    sendUpdate(new Update("bm", hash));
}
