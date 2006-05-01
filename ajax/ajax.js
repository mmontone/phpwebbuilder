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
function loadingStart(){
   color = document.getElementsByTagName('body')[0].style.backgroundColor;
}
function loadingStop(){
}

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
           alert("El sistema no est? funcionando, "+e);
       }
}

function goAjax(url, func, obj) {
  goAjaxMethod("GET", url, func, obj);
}

function ajaxError() {
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
      var http = getHTTPObject();
       http.abort();
       url = url;
       var params = encodeForm(formName);

       /*-------------------------------------------------------------------------
       ACA ABAJO ESTA EL PROMPT-------------------------------------------------*/

       prompt("url",url);prompt("paramans",params);
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
                           func(http.responseText, http.responseXML, obj);
                       }
               };
       try {
             http.send(params);
       } catch (e) {
           alert("El sistema no esta funcionando, "+e);
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
  var actions = xml.firstChild.childNodes;
  var i=0;
  for (; i< actions.length; i++) {
    eval("ajax_" + actions[i].tagName + "(actions[i]);");
  }
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
  var ret = false;
  if (action.getAttribute("id") != null)
      ret = document.getElementById(action.getAttribute("id"));
  if (action.getAttribute("path") != null)
    ret = nodeAtPath(action.getAttribute("path"));

  return ret;
}

function nodeAtPath(path_str) {
    var path = split(path_str, "/");
    var current_elem = document;
    for (var i=0; i < path.length; i++)
        current_elem = current_elem.childNodes[path[i]];
    return current_elem;
}

function ajax_insert_html(action) {
  var target = getActionTarget(action);
  target.innerHTML = xml2str(action.firstChild);
}

function ajax_replace_node(action) {
  var target = getActionTarget(action);
  var html = xml2html(action.firstChild);
  target.replaceChild(html, target.firstChild);
}

function ajax_replace_child(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  var html = xml2html(action.firstChild);
  target.replaceChild(html, child);
}

function ie_ajax_replace_child(action) {
  var target = getActionTarget(action);
  var child = getActionChild(action);
  var html = xml2html(action.firstChild);
  target.replaceChild(html, child);
}


function ajax_append_child(action) {
  var target = getActionTarget(action);
  var html = xml2html(action.firstChild);
  target.parentNode.appendChild(html);
}

function ie_ajax_append_child(action) {
    return ajax_append_child(action);
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
    url = "new_dispatch.php?"+action_id+"=execute";
    formName = "app";
    var form = document.getElementById(formName);
    form.setAttribute('action', url);
    form.submit();
}

function callActionAjax(action_id) {
    url = "new_dispatch.php?"+action_id+"=execute";
    formName = "app";
    postAjax(url,updatePage,formName);
}
