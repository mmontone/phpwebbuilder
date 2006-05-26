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

function componentChanged(comp,getValue,updateStrategy) {
    var id = checkbox.getAttribute('id');
    var value = getValue(comp);
    updateStrategy(new Update(id,value));
}

function componentFocus(comp, getValue, updateStrategy) {
    updateStrategy(new Update(comp.getAttribute('id'),"_ui_event_focus"));
}

function componentBlur(comp, getValue, updateStrategy) {
    updateStrategy(new Update(comp.getAttribute('id'),"_ui_event_blur"));
}

function checkboxGetValue(checkbox) {
    var value = checkbox.getAttribute('checked');
    if (!value)
        return "1";
    else
        return "0";
}

function inputGetValue(input) {
    return input.getAttribute('value');
}

function actionlinkGetValue(actionlink) {
    return actionlink.getAttribute('value');
}

function actionlink2GetValue(actionlink) {
    return actionlinkGetValue(actionlink);
}
