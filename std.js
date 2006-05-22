var updates = new Array();

function callAction(action_id) {
    var url = "new_dispatch.php?"+action_id+"=execute";
    submitFormToUrl(url);
}


function Update(t, v) {
    this.target = t;
    this.value = v;

    function getTarget() {
        return this.target;
    }

    function setTarget(t) {
        this.target = t;
    }

    function getValue() {
        return this.value;
    }

    function setValue(v) {
        this.value = v;
    }

    function printString() {
        return this.target + "=" + this.value;
    }
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
    var url = "new_dispatch.php?"+ update.printString();
    submitFormToUrl(url);
}

function enqueueUpdate(update) {
    updates.push(update);
}

function appendQueuedUpdates(url) {
    var s = updates.map(function(update){return update.target + "=" + update.value}).join("&");
    if (s != "")
        url = url + "&";
    return url + s;
}

function checkBoxChanged(checkbox,updateFunction) {
    var id = checkbox.getAttribute('id');
    var value = checkbox.getAttribute('checked') != 'checked';
    if (!value)
        updateFunction(new Update(id,"0"));
    else
        updateFunction(new Update(id + "1"));
}