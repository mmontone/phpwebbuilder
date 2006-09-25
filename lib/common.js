var onloadfs=new Array;

function addOnLoad(fun){
    onloadfs.push(fun);
}

window.onload=function (){
    for(var i=onloadfs.length-1; i>=0; i--){
        var f = onloadfs[i];
        f();
    }
}

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

function createSubmitForm() {
    var url = document.getElementById("app").getAttribute("action");
    var form = document.createElement("form");
    form.setAttribute("method","post");
    for (var target in updates) {
        appendUpdate(form,target,updates[target]);
    }
    var a = document.getElementById("app_class").getAttribute("value");
    appendUpdate(form, "app", a);
    form.setAttribute("action", url);
    return form;
}

function createSubmitData() {
    var a = document.getElementById("app_class").getAttribute("value");
    var str="app="+a;
    for (var target in updates) {
        str = str + "&"+target+"="+updates[target];
    }
    return str;
}

function appendUpdate(form, target, value) {
    var u = document.createElement("input");
    u.setAttribute("type","hidden");
    u.setAttribute("name", target);
    u.setAttribute("value", value);
    form.appendChild(u);
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
    sendEvent('changed', comp);
    return false;
}

function componentFocus(comp) {
    sendEvent('focus', comp);
    return false;
}

function componentBlur(comp) {
    sendEvent('blur', comp)
    return false;
}

function componentClicked(comp) {
    sendEvent('click', comp);
    return false;
}

function sendEvent(event, comp) {
    enqueueUpdate(new Update('event',event));
    sendUpdate(new Update('event_target', comp.getAttribute('id')));
}

function callback(comp, callback) {
	enqueueUpdate(new Update('event', callback));
	sendUpdate(new Update('event_target', comp));
}

function checkboxGetValue(checkbox) {
    if (checkbox.checked)
        return "1";
    else
        return "0";
}

function inputGetValue(input) {
    return input.value;
}

function dinputGetValue(input) {
    return input.value;
}

function passwordGetValue(input) {
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

function selectmultipleGetValue(select) {
	var opt_selected = new Array();
    var index = 0;
    for (var i=0;i < select.options.length;i++)
    {
	    if (select.options[i].selected)
        {
	        opt_selected[index] = select.options[i].value;
            index++;
        }
    }

    return opt_selected;
}

function selectmultipleGetValue(select) {
    var opt_selected = new Array();
    var index = 0;
    for (var i=0;i < select.options.length;i++)
    {
		if (select.options[i].selected)
        {
        	opt_selected[index] = select.options[i].value;
            index++;
		}
	}

    return opt_selected;
}

function radiobuttonGetValue(radiobutton) {
    if (radiobutton.checked)
        return "1";
    else
        return "0";
}

function getEvent(e){
	if (!e) var e = window.event;
    return e;
}
function getEventTarget(e2)
{
	var targ;
    var e = getEvent(e2);
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) {// defeat Safari bug
		targ = targ.parentNode;
	}
	while (!targ["on"+e.type]){ //Bubbling up
	   targ = targ.parentNode;
	}
	return targ;
}

function start_uploading(fieldname, filename){
	var ifr = document.getElementById(fieldname);
	var newf = document.createElement("span");
	newf.appendChild(document.createTextNode("uploading "+filename+"..."));
	ifr.parentNode.insertBefore(newf, ifr.nextSibling);
	ifr.style.visibility='hidden';
	ifr.style.width='0';
	ifr.style.height='0';
}
function end_uploading(fieldname, filename){
	var ifr = document.getElementById(fieldname).nextSibling;
	ifr.parentNode.replaceChild(document.createTextNode("uploaded "+filename), ifr);
}

function error_uploading(fieldname, filename){
	var ifr = document.getElementById(fieldname).nextSibling;
	ifr.parentNode.replaceChild(document.createTextNode("error uploading "+filename), ifr);
}


