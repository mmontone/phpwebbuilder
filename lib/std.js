function submitFormToUrl(url) {

    body = document.getElementsByTagName("body")[0];
    form = createSubmitForm(url);
    body.appendChild(form);
    form.submit();
}

function sendUpdate(update) {
    //var url = "new_dispatch.php?"+ update.target + "=" + update.value;
    enqueueUpdate(update);
    submitFormToUrl("new_dispatch.php");
}

function uploadFile() {}

