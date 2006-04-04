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
		return xmlhttp; }

var color;
function loadingStart(){
   color = document.getElementsByTagName('body')[0].style.backgroundColor;
//   document.getElementById('mainBody').style.background-color = 0xFFFFFF;
//   document.getElementById('mainBody').style.visibility="hidden";
}
function loadingStop(){
//   document.getElementById('mainBody').style.background-color = color;
//   document.getElementById('mainBody').style.visibility="visible";
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
                       		func(http.responseText, http.responseXML, obj);
                       	    loadingStop();
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

       //prompt("url",url);prompt("paramans",params);
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
		}else{
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

function callAjax(url) {
  goAjax(url, updatePage);
}

function submitAjax(form_id, url) {
  postAjax(url, updatePage, form_id);
}

function updatePage(text, xml) {
  var actions = xml.firstChild.childNodes;
  var i=0;
  for (; i< actions.length; i++) {
    eval("ajax" + actions[i].tagName + "(actions[i]);");
  }
}

function getActionTarget(action) {
  return document.getElementById(action.getAttribute("id"));
}

function ajaxreplace(action) {
  var target = getActionTarget(replace_action);
  var html = xml2html(replace_action.firstChild);
  target.parentNode.replaceChild(html, target);
}

function ajaxadd(add_action) {
  var target = getActionTarget(add_action);
  var html = xml2html(replace_action.firstChild);
  target.parentNode.addChild(html);
}

function ajaxremove(remove_action) {
  var target = getActionTarget(remove_action);
  target.parentNode.removeChild(target);
}



