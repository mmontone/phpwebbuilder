var onloadfs=new Array;
var onunloadfs=new Array;

function addOnLoad(fun){
    onloadfs.push(fun);
}

function addOnUnload(fun){
    onunloadfs.push(fun);
}

window.onload=function (){
    for(var i=onloadfs.length-1; i>=0; i--){
        var f = onloadfs[i];
        f();
    }
}

window.onunload=function (){
    for(var i=onunloadfs.length-1; i>=0; i--){
        var f = onunloadfs[i];
        f();
    }
}


document.window=window;
var updates = new Object();

function sendEvent(event, comp) {
    enqueueUpdate(new Update('event',event));
    sendUpdate(new Update('event_target', comp.getAttribute('id')));
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

function createSubmitForm() {
    var url = document.getElementById("app").getAttribute("action");
    var form = document.createElement("form");
    form.setAttribute("method","post");
    for (var target in updates) {
        appendUpdate(form,target,updates[target]);
    }
    var a = document.getElementById("app_class").getAttribute("value");
    var win = document.getElementById("window").getAttribute("value");
    appendUpdate(form, "app", a);
    appendUpdate(form, "window", win);
    form.setAttribute("action", url);
    return form;
}

function createSubmitData() {
    var a = document.getElementById("app_class").getAttribute("value");
    var win = document.getElementById("window").getAttribute("value");
    var str="app="+a+"&window="+win;

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
    return true;
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

/*
function checkboxGetValueInversed(checkbox) {
	if (checkbox.checked) {
	    return "1";
	}
	else {
	    return "0";
	}
}
*/

function inputGetValue(input) {
    return input.value;
}

function datetimeinputGetValue(input) {
	return input.value;
}

function dateinputGetValue(input) {
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
	if (e.type=='keyup') return targ;
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

var sendQueue = new Array();

function dataChanged(event){
	cancelPartialSend();
	sendQueue['timer'] = setTimeout('partialSend()', 500);
	sendQueue['element'] = getEventTarget(event);
}

function cancelPartialSend(){
	clearTimeout(sendQueue['timer']);
}

function partialSend(){
	triggerEventIn('change',sendQueue['element']);
}

function triggerEventIn(ev, elem){
	if( document.createEvent ) {
	    var evObj = document.createEvent('HTMLEvents');
		evObj.initEvent(ev, true, true );
		elem.dispatchEvent(evObj);
	} else {
		var evObj = document.createEventObject();
	    elem.fireEvent('on'+ev,evObj);
	}
}


function getRootWindow(){
	win = window;
	while(win.opener){
		win = win.opener;
	}
	return win;
}

function closeWindow(pos){
	 getWindowByName(pos).close();
}

function openWindow(pos, params){
	window.open(document.getElementById("app").getAttribute("action")+'&app='+document.getElementById("app_class").getAttribute("value")+'&window='+pos,pos, params);
}

function getWindowByName(name){
	if (name=='root'){
		return getRootWindow();
	} else {
		return window.open('',name);
	}
}

function refreshWindows(){
	for(var i = 0; i<arguments.length;i++){
		var win = getWindowByName(arguments[i]);
		if (win.name==arguments[i]) win.refresh();
	}
}

//------------------------------------

function str2html(str){
	var doc = document.createElement('div');
	doc.innerHTML= str;
	return doc.firstChild;
}

function xml2html(xml){
	return str2html(xml2str(xml));
}

function xml2str (xml) {
	if (xml.xml) {return xml.xml;}
	var ret =  new XMLSerializer().serializeToString(xml);
    return ret;
}


function reconstructTemplates(){
	alert(xml2str(document.getElementById('app')));
}

var contextMenus= [];
