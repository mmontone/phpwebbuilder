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
    // TODO: improve. Flag the modified fields only?
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

function componentChange(comp) {
    sendEvent('change', comp);
}

function componentFocus(comp) {
    sendEvent('focus', comp);
}

function componentBlur(comp) {
    sendEvent('blur', comp)
}

function componentClicked(comp) {
    sendEvent('click', comp);
}

function sendEvent(event, comp) {
    enqueueUpdate(new Update('event',event));
    sendUpdate(new Update('event_target', comp.getAttribute('id')));
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