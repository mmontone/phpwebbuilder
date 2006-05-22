function callAction(action_id) {
    url = "new_dispatch.php?"+action_id+"=execute";
    formName = "app";
    var form = document.getElementById(formName);
    form.setAttribute('action', url);
    form.submit();
}

function sendUpdate(update) {
    url = "new_dispatch.php?"+ update;
    formName = "app";
    var form = document.getElementById(formName);
    form.setAttribute('action', url);
    form.submit();
}

function checkBoxChanged(checkbox) {
    id = checkbox.getAttribute('id');
    value = checkbox.getAttribute('checked') != 'checked';
    if (!value)
        sendUpdate(id + "=0");
    else
        sendUpdate(id + "=1");
}