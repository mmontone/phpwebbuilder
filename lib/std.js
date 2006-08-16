function submitForm() {

    var body = document.getElementsByTagName("body")[0];
    var form = createSubmitForm();
    body.appendChild(form);
    form.submit();
}

function sendUpdate(update) {
    //var url = "dispatch.php?"+ update.target + "=" + update.value;
    enqueueUpdate(update);
    submitForm();
}
