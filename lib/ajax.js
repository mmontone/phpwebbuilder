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

function goAjaxMethod(met, url, func, obj) {
      loadingStart();
      var http = getHTTPObject();
       http.abort();
       url = url;
       try {
               http.open(met, url, true);
       } catch (e) {
         alert("El sistema no esta funcionando, "+e);
       }
       http.onreadystatechange = function () {
                       if (http.readyState==4) {
                            loadingStop();
                       		if (inIE()) {
                       				if (http.responseXML.firstChild == null || !func(http.responseText, http.responseXML, obj))
			                             ajaxError(http.responseText);
	    		               }
	            		       else {
                        		   if (http.responseXML == null || !func(http.responseText, http.responseXML, obj))
		                             ajaxError(http.responseText);
        		               }
						}
               };
       try {
             http.send(null);
       } catch (e) {
           ajaxError(http.responseText);
       }
}

function goAjax(url, func, obj) {
  goAjaxMethod("GET", url, func, obj);
}

function ajaxError(msg) {
    alert("Error: " + msg);
}


function encodeForm(form) {
  var ret = "";
  var elems = form.elements;
  for(var i = 0;i< elems.length; i++) {
    if (!(elems[i].type=="checkbox" && elems[i].checked==false))
      ret += "&" + elems[i].name + "=" + elems[i].value;
  }
  return ret;
}

function appendDebug(str){
   var db = document.getElementById("debug");
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
   db.appendChild(document.createTextNode(str));
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


function postAjax(form, func, obj) {
       var params = encodeForm(form);
       postToAjax(params, form.getAttribute("action"), func, obj);
}

function time(){
	return new Date().getSeconds() + ":" + new Date().getMilliseconds();
}
function postToAjax(params,url,func, obj) {
      var http = getHTTPObject();
       //prompt('params',params);
       loadingStart(ajax_queue.length);
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
                              loadingStop(ajax_queue.length);
                           } else {
                               appendDebug("");
                               //appendDebug(http.responseText);
	                           func(http.responseText, http.responseXML, obj);
	                           loadingStop(ajax_queue.length);
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
           loadingStop(ajax_queue.length);
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

function callAjax(url) {
  if (inIE())
    goAjax(url, ie_updatePage);
  else
    goAjax(url, updatePage);
}

function submitAjax(form_id, url) {
  postAjax(url, updatePage, form_id);
}

function updatePage(text, xml) {
    if (inIE()) {
        return ie_updatePage(text,xml);
    }
    else {
        return others_updatePage(text,xml);
    }
}

function others_updatePage(text, xml) {
    var acts = xml.firstChild;
    var actions = acts.childNodes;
    var i=0;
    for (; i< actions.length; i++) {
	  var str = "ajax_" + actions[i].tagName + "(actions[i]);";
	  eval(str);
   }
  return true;
}

function ie_updatePage(text, xml) {
  var actions = xml.firstChild.nextSibling.childNodes;
  var i=0;
  for (; i< actions.length; i++) {
    //alert(actions[i].nodeName);
    eval("ie_ajax_" + actions[i].nodeName + "(actions[i]);");
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
	//alert(xml2str(action));
	/*alert("Func: " + action.getAttribute("f"));*/
	fcall = action.getAttribute("f") + "(";

	params = action.firstChild.childNodes;

	if (params.length > 0) {
		for (var i=0; i< params.length - 1; i++) {
	      /*alert("Param: " + params[i].firstChild.nodeValue);*/
	      fcall += "\"" + params[i].firstChild.nodeValue + "\",";
	    }
	    fcall += "\"" + params[params.length - 1].firstChild.nodeValue + "\"";
    }
    fcall +=");";
    //alert(fcall);
    eval(fcall);
}

function ie_ajax_call(action) {
	return ajax_call(action);
}

function ajax_insert_html(action) {
  var target = getActionTarget(action);
  target.innerHTML = xml2str(getActionChild(action));
}

function ajax_repn(action) {
  var target = getActionTarget(action);
  var html = xml2html(getActionChild(action));
  target.parentNode.replaceChild(html, target);
}

function ie_ajax_repn(action) {
    var target = getActionTarget(action);
    var html = xml2str(getActionChild(action));
    target.outerHTML = html;
}

function ie_ajax_undefined(action) {
    ajax_undefined(action);
}

function ajax_undefined(action) {
      ajaxError("Undefined action: " + action);
      appendDebug("");
      loadingStop();
}


function ajax_append(action) {
  var target = getActionTarget(action);
  var html = xml2html(getActionChild(action));
  target.appendChild(html);
}

function ie_ajax_append(action) {
    var target = getActionTarget(action);
    var child = document.createElement("");
    target.appendChild(child);
    child.outerHTML = xml2str(getActionChild(action));

}

function ajax_rem(action) {
  var target = getActionTarget(action);
  target.parentNode.removeChild(target);
}

function ie_ajax_rem(action) {
    return ajax_rem(action);
}

function ajax_insert(action) {
  var target = getActionTarget(action);
  var child = xml2html(getActionChild(action));
  target.parentNode.insertBefore(child, target);
}

function ie_ajax_insert(action) {
   var target = getActionTarget(action);
   var child = document.createElement();
   target.parentNode.insertBefore(child, target);
   child.outerHTML = xml2str(getActionChild(action));
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

function ie_ajax_setatt(action) {
    return ajax_setatt(action);
}

function ajax_rematt(action) {
    var target = getActionTarget(action);
    var attribute = action.childNodes[0].firstChild.data;
    target.removeAttribute(attribute);
}

function ie_ajax_rematt(action) {
    return ajax_rematt(action);
}

function ajax_bookmark(action) {}
function ie_ajax_bookmark(action) {}

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

function callAction(action_id) {
    url = "Action.php?" +action_id+"=execute";
    act = document.getElementById(action_id);
    old = act.getAttribute("value");
    act.setAttribute("value", "execute");
    formName = "app";
    postAjax(url,updatePage,formName);
    act.setAttribute("value", old);
}

function postInAjax(){
    url = "Action.php";
    formName = "app";
    var form = document.getElementById(formName);
    postAjax(url,updatePage,form);
    return false;
}


function push(time){
    f = function (){
       sendEvent("push","app.comp.main");
    }
    setTimeout("f()",time);
}

function sendUpdate(update) {
    enqueueUpdate(update);
    enqueueUpdate(new Update("ajax", "true"));
    postToAjax(createSubmitData(),document.getElementById("app").getAttribute("action"),updatePage);
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
