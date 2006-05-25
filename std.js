var updates = new Array();

function callAction(action_id) {
    var url = "new_dispatch.php?"+action_id+"=execute";
    submitFormToUrl(url);
}

function submitFormToUrl(url) {
    url = appendQueuedUpdates(url);
    alert(url);
    var formName = "app";
    var form = document.getElementById(formName);
    form.setAttribute('action', url);
    form.submit();
}

function sendUpdate(update) {
    var url = "new_dispatch.php?"+ update.target + "=" + update.value;
    submitFormToUrl(url);
}

function enqueueUpdate(update) {
    updates.push(update);
}

function appendQueuedUpdates(url) {
    var s = updates.map(function(update){return update.target + "=" + update.value}).join("&");
    return url + "&" + s;
}

