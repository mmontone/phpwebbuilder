function callAction(action_id) {
    var url = "new_dispatch.php?"+action_id+"=execute";
    submitFormToUrl(url);
}

function submitFormToUrl(url) {
    url = appendQueuedUpdates(url);
    var formName = "app";
    var form = document.getElementById(formName);
    form.setAttribute('action', url);
    form.submit();
}

function sendUpdate(update) {
    var url = "new_dispatch.php?"+ update.target + "=" + update.value;
    submitFormToUrl(url);
}

function uploadFile() {}

