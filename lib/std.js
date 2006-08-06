function submitForm() {

    body = document.getElementsByTagName("body")[0];
    form = createSubmitForm();
    body.appendChild(form);
    form.submit();
}

function sendUpdate(update) {
    //var url = "dispatch.php?"+ update.target + "=" + update.value;
    enqueueUpdate(update);
    submitForm();
}
