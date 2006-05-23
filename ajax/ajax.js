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


function encodeForm(formName) {
  var form = document.getElementById(formName);
  var ret = "";
  var elems = form.elements;
  for(var i = 0;i< elems.length; i++) {
    if (!(elems[i].type=='checkbox' && elems[i].checked==false))
      ret += "&" + elems[i].name + "=" + elems[i].value;
  }
  return ret;
}

function postAjax(url, func, formName, obj) {
      loadingStart();
      var http = getHTTPObject();
       http.abort();
       url = url;
       var params = encodeForm(formName);

       /*-------------------------------------------------------------------------
       ACA ABAJO ESTA EL PROMPT-------------------------------------------------*/

       //       prompt("url",url);prompt("paramans",params);
//       prompt("url", url + params);
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
                           if (!http.responseXML) {
                              ajaxError();
                              loadingStop();
                           }
                           /* DEBUG */
	                           /* db = document.getElementById('debug');
	                           if (!db) {
		                           db = document.createElement('div');
		                           db.setAttribute('id', 'debug');
		                           form = document.getElementById(formName);
		                           form.appendChild(db);
		                           t = document.createTextNode(http.responseText);
		                           db.appendChild(t);
	                           } else {
		                           t = document.createTextNode(http.responseText);
		                           db.replaceChild(t,db.firstChild);
	                           }*/

                           /* END DEBUG */

                           func(http.responseText, http.responseXML, obj);
                           loadingStop();
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

function ajax_replace_node(action) {
  var target = getActionTarget(action);
  var html = xml2html(getActionChild(action));
  target.parentNode.replaceChild(html, target);
}

function ie_ajax_replace_node(action) {
    var target = getActionTarget(action);
    var html = xml2str(getActionChild(action));
    target.outerHTML = html;
}

/*
function ajax_replace_child(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  var html = xml2html(getActionChild(action));
  target.replaceChild(html, child);
}

function ie_ajax_replace_child(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  var html = xml2html(getActionChild(action));
  target.replaceChild(html, child);
}
*/


function ajax_append_child(action) {
  var target = getActionTarget(action);
  var html = xml2html(getActionChild(action));
  target.appendChild(html);
}

function ie_ajax_append_child(action) {
    var target = getActionTarget(action);
    var child = document.createElement('');
    target.appendChild(child);
    child.outerHTML = xml2str(getActionChild(action));

}

function ajax_remove_node(action) {
  var target = getActionTarget(action);
  target.parentNode.removeChild(target);
}

function ie_ajax_remove_node(action) {
    return ajax_remove_node(action);
}

function ajax_remove_child(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  target.removeChild(child);
}

function ie_ajax_remove_child(action) {
    return ajax_remove_child(action);
}

function ajax_insert_before(action) {
  var target = getActionTarget(action);
  var child = xml2html(getActionChild(action));
  target.parentNode.insertBefore(child, target);
}

function ie_ajax_insert_before(action) {
   var target = getActionTarget(action);
   var child = document.createElement();
   target.parentNode.insertBefore(child, target);
   child.outerHTML = xml2str(getActionChild(action));
}

function ajax_set_attribute(action) {
    var target = getActionTarget(action);
    var attribute = action.childNodes[0].firstChild.data;
    var value = action.childNodes[1].firstChild.data;
    target.setAttribute(attribute, value);
}

function ie_ajax_set_attribute(action) {
    return ajax_set_attribute(action);
}

function ajax_remove_attribute(action) {
    var target = getActionTarget(action);
    var attribute = action.childNodes[0].firstChild.data;
    target.removeAttribute(attribute);
}

function ie_ajax_remove_attribute(action) {
    return ajax_remove_attribute(action);
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
    url = "new_dispatch.php?" +action_id+"=execute";
    act = document.getElementById(action_id);
    old = act.getAttribute('value');
    act.setAttribute('value', 'execute');
    formName = "app";
    postAjax(url,updatePage,formName);
    act.setAttribute('value', old);
}

function postInAjax(){
    url = "new_dispatch.php";
    formName = "app";
    postAjax(url,updatePage,formName);
    return false;
}


function push(){
    callback= function (str,xml){
        updatePage(str,xml);
        push();
    }
    f = function (){
       postAjax("new_dispatch.php",callback,"app");
    }
    setTimeout('f()',60000);
}

//push();



function sendUpdate(update) {
    var url = "new_dispatch.php?"+ update.target + "=" + update.value;
    alert(url);
    postAjax(url,updatePage,"app");
}

function enqueueUpdate(update) {
    sendUpdate(update);
}