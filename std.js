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

function uploadFile() {}

