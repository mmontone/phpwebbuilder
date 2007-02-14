function getHTTPObject() {
  var xmlhttp; /** Special IE only code ... */
  /*@cc_on @if (@_jscript_version >= 5) try
    { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
    catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
     catch (E) { xmlhttp = false; } } @else
    xmlhttp = false; @end @*/
    /** Every other browser on the planet */
    if (!xmlhttp && typeof XMLHttpRequest != "undefined")
    { try { xmlhttp = new XMLHttpRequest(); }
    catch (e)
    { xmlhttp = false; }
    }
    return xmlhttp;
}

var color;
function loadingStart(){}
function loadingStop(){}

function ajaxError(msg) {
    alert("Error: " + msg);
}


function appendDebug(str){
/*   var db = document.getElementById("debug");
   if (!db) {
       db = document.createElement("label");
//       db = document.createElement("div");
       db.setAttribute("id", "debug");
       form = document.getElementById("app");
       form.firstChild.appendChild(db);
	    db.style.height='100px';
   } else {
	 db.removeChild(db.firstChild);
   }
   db.appendChild(document.createTextNode(str));*/
}
var ajax_queue = new Array;
function enqueue(elem){
    ajax_queue.push(elem);
}
function dequeue(elem){
    for(var j=0; j<ajax_queue.length; j++){
       if (ajax_queue[j]==elem) break;
    }
    ajax_queue.splice(j,1);
}
function cancel_ajax(){
	var http = ajax_queue.pop();
	http.abort();
}


function time(){
	return new Date().getSeconds() + ":" + new Date().getMilliseconds();
}
function postToAjax(params,url,func, showLoading) {
      var http = getHTTPObject();
       //prompt('params',params);
       if(showLoading)loadingStart(ajax_queue.length);
       enqueue(http);
       http.abort();
       try {
          http.open("POST", url, true);
          http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          http.setRequestHeader("Content-length", params.length);
          http.setRequestHeader("Connection", "close");
       } catch (e) {
         alert("El sistema no esta funcionando, "+e);
       }
       //var ret = "started "+time();
       http.onreadystatechange = function () {
                       //ret += " state "+http.readyState+ " on time "+time();
                       if (http.readyState==4) {
                           dequeue(http);
                           if (!http.responseXML) {
                              ajaxError(http.responseText);
                              appendDebug(http.responseText);
                              if(showLoading)loadingStop(ajax_queue.length);
                           } else {
                               appendDebug("");
                               //appendDebug(http.responseText);
	                           func(http.responseText, http.responseXML);
	                           if(showLoading)loadingStop(ajax_queue.length);
	                           onUpdate();
                           }
                           //ret += " ended on "+time();
                           //alert(ret);
                       }
               };
       try {
             http.send(params);
       } catch (e) {
           ajaxError(http.responseText);
           if(showLoading)loadingStop(ajax_queue.length);
       }
}

function inIE() {
	return navigator.appName == "Microsoft Internet Explorer";
}

function inOpera() {
	return navigator.appName == "Opera";
}

function updatePage(text, xml) {
    var fc = xml.firstChild;
	while(fc.nodeName!='ajax'){fc = fc.nextSibling;}
    return all_updatePage(text, fc.childNodes);
}

function all_updatePage(text, actions) {
   var i=0;
   for (; i< actions.length; i++) {
	  var str = "ajax_" + actions[i].tagName + "(actions[i]);";
	  eval(str);
   }
   return true;
}

function getActionTarget(action) {
  if (action.getAttribute("id") != null)
    return document.getElementById(action.getAttribute("id"));
  return false;
}

function getActionChild(action) {
    var e = action.firstChild;
    while(e.data && e.nextSibling){
        e = e.nextSibling;
    }
    return e;
}

function ajax_call(action) {
	fcall = action.getAttribute("f") + "(";
	params = action.firstChild.childNodes;
	if (params.length > 0) {
		for (var i=0; i< params.length - 1; i++) {
	      fcall += "\"" + params[i].firstChild.nodeValue + "\",";
	    }
	    fcall += "\"" + params[params.length - 1].firstChild.nodeValue + "\"";
    }
    fcall +=");";
    eval(fcall);
}

function ajax_script(action){
	eval(action.firstChild.nodeValue);
}

function ajax_repn(action) {
  var target = getActionTarget(action);
  do_repn(target,xml2str(getActionChild(action)));
}

function do_repn(target,str){
  if(inIE()){
     target.outerHTML = str;
  } else {
     var html = str2html(str);
     target.parentNode.replaceChild(html, target);
  }
}

function ajax_undefined(action) {
      ajaxError("Undefined action: " + action);
      appendDebug("");
      loadingStop();
}


function ajax_append(action) {
  var target = getActionTarget(action);
  var str = xml2str(getActionChild(action));
  do_append(target,str);
}

function do_append(target,str){
  if(inIE()){
     var child = document.createElement("");
     target.appendChild(child);
     child.outerHTML = str;
  } else {
     var html = str2html(str);
     target.appendChild(html);
  }
}


function ajax_rem(action) {
  var target = getActionTarget(action);
  do_rem(target);
}

function do_rem(target) {
  if (target)
	 target.parentNode.removeChild(target);
}


function ajax_insert(action) {
  var target = getActionTarget(action);
  var str = xml2str(getActionChild(action));
  do_insert(target,str);
}

function do_insert(target,str){
  if(inIE()){
    var child = document.createElement();
    target.parentNode.insertBefore(child, target);
    child.outerHTML = str;
  } else {
    var child = str2html(str);
    target.parentNode.insertBefore(child, target);
  }
}


function ajax_setatt(action) {
    var target = getActionTarget(action);
    var attribute = action.childNodes[0].firstChild.data;
    if (action.childNodes[1].hasChildNodes()){
	    var value = action.childNodes[1].firstChild.data;
	} else {
		var value = "";
	}
	do_setatt(target,attribute,value);
}

function do_setatt(target,attribute,value){
    if (attribute=="class"){
	    target.className=value;
    } else {
	    target.setAttribute(attribute, value);
    }
}

function ajax_rematt(action) {
    var target = getActionTarget(action);
    var attribute = action.childNodes[0].firstChild.data;
    do_rematt(target,attribute);
}

function do_rematt(target,attribute){
    target.removeAttribute(attribute);
}

function ajax_bookmark(action) {
    var h = action.getAttribute("hash");
	do_bookmark(h);
}

function do_bookmark(hash) {}

function navigateClicked(target){
	return componentClicked(target);
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


/*--------------------------*/

function push(time,count){
	if (count==0) return;
    f = function (){
	   refresh();
       push(time,count-1);
    }
    setTimeout("f()",time);
}

function sendUpdate(update) {
    enqueueUpdate(update);
    refresh();
    updates = new Object();
}

var onupdates=new Array;

function addOnUpdate(fun){
    onupdates.push(fun);
}

addOnUpdate(function(){
	var att = document.getElementById("push")
   if (att != null){
		push(att.getAttribute("value"));
   }
});

function onUpdate(){
    for(var i=onupdates.length-1; i>=0; i--){
        var f = onupdates[i];
        f();
    }
}

addOnLoad(onUpdate);

function refresh(){
    enqueueUpdate(new Update("ajax", "true"));
	postToAjax(createSubmitData(),document.getElementById("app").getAttribute("action"),updatePage,true);
}


function openWindow(pos, params){
	window.open(
		document.getElementById("app").getAttribute("action")
		+'&app='+
		document.getElementById("app_class").getAttribute("value")
		+'&window='+pos,
		pos, params);
}
