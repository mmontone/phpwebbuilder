function getHTTPObject() {
  var xmlhttp; /** Special IE only code ... */
  /*@cc_on @if (@_jscript_version >= 5) try
    { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
    catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
     catch (E) { xmlhttp = false; } } @else
    xmlhttp = false; @end @*/
    /** Every other browser on the planet */
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
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
			                             ajaxError();
	    		               }
	            		       else {
                        		   if (http.responseXML == null || !func(http.responseText, http.responseXML, obj))
		                             ajaxError();
        		               }
						}
               };
       try {
             http.send(null);
       } catch (e) {
           ajaxError();
       }
}

function goAjax(url, func, obj) {
  goAjaxMethod("GET", url, func, obj);
}

function ajaxError() {
    alert("Error");
}


function encodeForm(form) {
  var ret = "";
  var elems = form.elements;
  for(var i = 0;i< elems.length; i++) {
    if (!(elems[i].type=='checkbox' && elems[i].checked==false))
      ret += "&" + elems[i].name + "=" + elems[i].value;
  }
  return ret;
}

function appendDebug(str){
   db = document.getElementById('debug');
   if (!db) {
       db = document.createElement('div');
       db.setAttribute('id', 'debug');
       form = document.getElementById("app");
       form.appendChild(db);
       t = document.createTextNode(str);
       db.appendChild(t);
   } else {
       t = document.createTextNode(str);
       db.replaceChild(t,db.firstChild);
   }
}
ajax_queue = new Array;
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
       postToAjax(params, form.getAttribute('action'), func, obj);
}
function postToAjax(params,url,func, obj) {
      var http = getHTTPObject();
       enqueue(http);
       loadingStart();
       http.abort();
       try {
          http.open("POST", url, true);
          http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          http.setRequestHeader("Content-length", params.length);
          http.setRequestHeader("Connection", "close");
       } catch (e) {
         alert("El sistema no esta funcionando, "+e);
       }
       http.onreadystatechange = function () {
                       if (http.readyState==4) {
                           dequeue(http);
                           if (!http.responseXML) {
                              ajaxError();
                              appendDebug(http.responseText);
                              loadingStop();
                           } else {
                               appendDebug('');
	                           func(http.responseText, http.responseXML, obj);
	                           loadingStop();
                           }
                       }
               };
       try {
             http.send(params);
       } catch (e) {
           ajaxError();
           loadingStop();
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
//    if (acts){
    var actions = acts.childNodes;
    var i=0;
    for (; i< actions.length; i++) {
        //    if (!actions[i].data){
	  str = "ajax_" + actions[i].tagName + "(actions[i]);";
	  eval(str);
//    }
    }
//}
  return true;
}

function ie_updatePage(text, xml) {
  var actions = xml.firstChild.nextSibling.childNodes;
  var i=0;
  for (; i< actions.length; i++) {
    eval("ie_ajax_" + actions[i].nodeName + "(actions[i]);");
  }
  return true;
}


function getActionTarget(action) {
  if (action.getAttribute("id") != null)
    return document.getElementById(action.getAttribute("id"));
  if (action.getAttribute("path") != null)
    return nodeAtPath(action.getAttribute("path"));
  return false;
}

function getActionChild(action) {
    e = action.firstChild;
    while(e.data){
        e = e.nextSibling;
    }
    return e;
}


function nodeAtPath(path_str) {
    var path = path_str.split("/");
    var current_elem = document.getElementById("app");
    //navigateInteractivelyFrom(current_elem);
    for (var i=1; i < path.length - 1; i++) {
        //alert(path[i]);
        current_elem = current_elem.childNodes[path[i]];
        //alert(current_elem);
    }
    return current_elem;
}

function navigateInteractivelyFrom(aNode) {
    var action = prompt('Action:','childNodes[0]');
    var result;

    while (action) {
        result = eval("aNode." + action + ";");
        alert(result);
        action = prompt('Action:','firstChild');
    }
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

/*
function ajax_repc(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  var html = xml2html(getActionChild(action));
  target.replaceChild(html, child);
}

function ie_ajax_repc(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  var html = xml2html(getActionChild(action));
  target.replaceChild(html, child);
}
*/

function ie_ajax_undefined(action) {
    ajax_undefined(action);
}

function ajax_undefined(action) {
      ajaxError();
      appendDebug('');
      loadingStop();
}


function ajax_append(action) {
  var target = getActionTarget(action);
  var html = xml2html(getActionChild(action));
  target.appendChild(html);
}

function ie_ajax_append(action) {
    var target = getActionTarget(action);
    var child = document.createElement('');
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
    var value = action.childNodes[1].firstChild.data;
    target.setAttribute(attribute, value);
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
    url = "dispatch.php?" +action_id+"=execute";
    act = document.getElementById(action_id);
    old = act.getAttribute('value');
    act.setAttribute('value', 'execute');
    formName = "app";
    postAjax(url,updatePage,formName);
    act.setAttribute('value', old);
}

function postInAjax(){
    url = "dispatch.php";
    formName = "app";
    var form = document.getElementById(formName);
    postAjax(url,updatePage,form);
    return false;
}


function push(){
    callback= function (str,xml){
        updatePage(str,xml);
        push();
    }
    f = function (){
       postAjax("dispatch.php",callback,"app");
    }
    setTimeout('f()',60000);
}

//push();

function sendUpdate(update) {
    enqueueUpdate(update);
    postToAjax(createSubmitData(),"dispatch.php",updatePage);
}

function uploadFile(fileelement){
    var body = document.getElementsByTagName('body')[0];
    window.onload=function (){alert("loaded");}
    var ifr = document.createElement('iframe');
    var div = document.createElement('div');
    var based = document.getElementById("basedir").getAttribute('value');
    ifr.src='admin/ajax/uploadFile.php?filenamefield='+fileelement+
    '&basedir='+based;
    div.style.visibility="hidden";
    body.appendChild(div);
    div.appendChild(ifr);

}
function start_uploading(fieldname){
	movf = document.getElementById(fieldname);
	newf = document.createElement('span');
	newf.appendChild(document.createTextNode("uploading "+movf.value+"..."));
	for(var i in movf.attributes){
		att = movf.attributes[i].nodeName;
		newf.setAttribute(att, movf.getAttribute(att));
	}
	movf.parentNode.replaceChild(newf, movf);
}
function end_uploading(fieldname, filename){
	text = document.getElementById(fieldname);
	text.replaceChild(document.createTextNode("uploaded "+filename), text.firstChild);
}