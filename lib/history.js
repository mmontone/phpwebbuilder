
function initialize_history(){
    dhtmlHistory.initialize();
    this.fireOnNewListener = false;
    dhtmlHistory.addListener(historyChange);
    if (dhtmlHistory.isFirstLoad() || dhtmlHistory.getCurrentLocation() == "") {
        dhtmlHistory.add(document.getElementById("bookmark").getAttribute("value"));
    }
 }

function historyChange(newLocation, historyData) {
	if (dhtmlHistory.getCurrentLocation() == ""){
        dhtmlHistory.add(document.getElementById("bookmark").getAttribute("value"));
	} else {
	    sendUpdate(new Update("bm", newLocation));
    }
}


addOnLoad(initialize_history);

function do_bookmark(hash){
    dhtmlHistory.add(hash, null);
}

function ie_ajax_bookmark(action) {
    return ajax_bookmark(action);
}

function load_bookmark(hash){
    dhtmlHistory.add(hash, null);
    sendUpdate(new Update("bm", hash));
}
