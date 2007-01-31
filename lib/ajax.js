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
       var ret = "started "+time();
       http.onreadystatechange = function () {
                       ret += " state "+http.readyState+ " on time "+time();
                       if (http.readyState==4) {
                           dequeue(http);
                           //alert(http.responseText);
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
                           ret += " ended on "+time();
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


//------------------------------------

function xml2html(xml){
    if (xml.nodeName=="#text") {
      return document.createTextNode(xml.nodeValue);
    }
    else{
        var html = document.createElement(xml.tagName);
        var childs = xml.childNodes;
        var i=0;
        for (; i< childs.length; i++) {
          var child = xml2html(childs[i]);
          html.appendChild(child);
        }
        var attrs = xml.attributes;
        for (var i=0; i<  attrs.length; i++) {
            html.setAttribute(attrs[i].nodeName, attrs[i].nodeValue);
        }
        return html;
    }
}

function inIE() {
	return navigator.appName == "Microsoft Internet Explorer";
}

function inOpera() {
	return navigator.appName == "Opera";
}

function updatePage(text, xml) {
    if (inIE()) {
        return all_updatePage(text, xml.firstChild.nextSibling.childNodes);
    }
    else if (inOpera()) {
        return all_updatePage(text, xml.firstChild.nextSibling.nextSibling.childNodes);
    } else {
        return all_updatePage(text, xml.firstChild.childNodes);
    }
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

function ajax_repn(action) {
  var target = getActionTarget(action);
  do_repn(target,getActionChild(action));
}

function do_repn(target,xml){
  if(inIE()){
     var html = xml2str(xml);
     target.outerHTML = html;
  } else {
     var html = xml2html(xml);
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
  if(inIE()){
     var child = document.createElement("");
     target.appendChild(child);
     child.outerHTML = xml2str(getActionChild(action));
  } else {
     var html = xml2html(getActionChild(action));
     target.appendChild(html);
  }


}


function ajax_rem(action) {
  var target = getActionTarget(action);
  target.parentNode.removeChild(target);
}

function ajax_insert(action) {
  var target = getActionTarget(action);
  if(inIE()){
    var child = document.createElement();
    target.parentNode.insertBefore(child, target);
    child.outerHTML = xml2str(getActionChild(action));
  } else {
    var child = xml2html(getActionChild(action));
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
    if (attribute=="class"){
	    target.className=value;
    } else {
	    target.setAttribute(attribute, value);
    }
}

function ajax_rematt(action) {
    var target = getActionTarget(action);
    var attribute = action.childNodes[0].firstChild.data;
    target.removeAttribute(attribute);
}


function ajax_bookmark(action) {}

function navigateClicked(target){
	return componentClicked(target);
}

function xml2str (xml) {
	if (xml.nodeName=="#text")
      return xml.nodeValue;

    var str = "<" + xml.nodeName;
    var attrs = xml.attributes;
    for (var i=0; i<  attrs.length; i++) {
      str += " " + attrs[i].nodeName + "=\"" +  attrs[i].nodeValue + "\"";
    }
    str +=">";
    for (var i=0; i< xml.childNodes.length; i++) {
      str += xml2str(xml.childNodes[i]);
    }
    str += "</" + xml.nodeName + ">";
    return str;
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
