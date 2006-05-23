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

function componentChanged(comp,updateFunction) {
    var id = checkbox.getAttribute('id');
    var value = checkbox.getAttribute('value');
    updateFunction(new Update(id,value));
}

function checkBoxChanged(checkbox,updateFunction) {
    var id = checkbox.getAttribute('id');
    var value = checkbox.getAttribute('checked');
    if (!value)
        updateFunction(new Update(id,"1"));
    else
        updateFunction(new Update(id,"0"));
}

function radioButtonChanged(radioButton,updateFunction) {
    return checkBoxChanged(radioButton,updateFunction);
}
