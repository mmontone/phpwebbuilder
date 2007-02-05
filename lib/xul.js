function quit (aForceQuit)
{
  var appStartup = Components.classes['@mozilla.org/toolkit/app-startup;1'].
    getService(Components.interfaces.nsIAppStartup);

  // eAttemptQuit will try to close each XUL window, but the XUL window can cancel the quit
  // process if there is unsaved data. eForceQuit will quit no matter what.
  var quitSeverity = aForceQuit ? Components.interfaces.nsIAppStartup.eForceQuit :
                                  Components.interfaces.nsIAppStartup.eAttemptQuit;
  appStartup.quit(quitSeverity);
}

function str2html(str){
  xulData="<box id='dataBox' xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
          str + "</box>";

  var parser=new DOMParser();
  var resultDoc=parser.parseFromString(xulData,"text/xml");
  return resultDoc.documentElement.firstChild;
 }

function openWindow(pos, params){
	window.open(
		'Action.php?'
		+'&app='+
		document.getElementById("app_class").getAttribute("value")
		+'&window='+pos,
		pos, params);
}

