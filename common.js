document.window=window;
var updates = new Object();

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

function enqueueUpdate(update) {
    //eval("updates." + update.target + "=update.value");
    updates[update.target] = update.value;
}

function enqueueChange(comp,getValue) {
    var target = comp.getAttribute('id');
    var value = getValue(comp);
    enqueueUpdate(new Update(target,value));
}

function appendQueuedUpdates(url) {
    var s = "";
    for (var target in updates) {
        s += "&" + target + "=" + updates[target];
    }
    return url + s;
}

function componentChange(comp,getValue) {
    var target = comp.getAttribute('id');
    var value = getValue(comp);
    sendUpdate(new Update(target,value));
}

function componentFocus(comp) {
    sendUpdate(new Update(comp.getAttribute('id'),"_ui_event_focus"));
}

function componentBlur(comp) {
    sendUpdate(new Update(comp.getAttribute('id'),"_ui_event_blur"));
}

function checkboxGetValue(checkbox) {
    if (!checkbox.checked)
        return "1";
    else
        return "0";
}

function inputGetValue(input) {
    return input.value;
}

function actionlinkGetValue(actionlink) {
    return actionlink.value;
}

function actionlink2GetValue(actionlink) {
    return actionlinkGetValue(actionlink);
}

function textareacomponentGetValue(textarea) {
    return textarea.value;
}

function selectGetValue(select) {
    return select.selectedIndex;
}

function radiobuttonGetValue(radiobutton) {
    if (radiobutton.checked)
        return "1";
    else
        return "0";
}