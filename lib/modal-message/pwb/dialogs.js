function openNotificationDialog(message, callback_comp, pwb_url) {
	dialog = new DHTML_modalMessage();
	dialog.setShadowOffset(5);
	dialog.setSize(400,150);
	dialog.setShadowDivVisible(true);
	html = ""
	html += "<table width=\"100%\" border=\"0\">"
	html +=	"<tr>"
	html +="<td rowspan=\"2\"><img src=\"" + pwb_url + "/DefaultCMS/Templates/Default/icons/Neu/48x48/status/dialog-information.png\"></td>"
	html += "<td><h3>" + message + "</h3></td>"
	html += "</tr>"
	html +="<tr>"
	html +="<td align=\"right\"><input type=\"button\" value=\"Aceptar\" style=\"width:60px\" onclick=\"dialog.close();sendAccept('" + callback_comp + "');return false;\">"
	html +="</tr>"
	html +="</table>"
	dialog.setHtmlContent(html);
	dialog.display();
}

function openPromptDialog(message, text, callback_comp, pwb_url) {
	dialog = new DHTML_modalMessage();
	dialog.setShadowOffset(5);
	dialog.setSize(400,150);
	dialog.setShadowDivVisible(true);
	html = ""
	html += "<table width=\"100%\" border=\"0\">"
	html +=	"<tr>"
	html +="<td rowspan=\"2\"><img src=\"" + pwb_url + "/DefaultCMS/Templates/Default/icons/Neu/48x48/status/dialog-information.png\"></td>"
	html += "<td><h3>" + message + "</h3></td>"
	html += "</tr>"
	html +=	"<tr>"
	html += "<td><input type=\"text\" value=\"" + text + "\" id=\"prompt_input\"/></td>"
	html += "</tr>"
	html +="<tr>"
	html +="<td align=\"right\"><input type=\"button\" value=\"Cancelar\" style=\"width:60px\" onclick=\"dialog.close();sendCancel('" + callback_comp + "');return false;\"></td><td><input type=\"button\" value=\"Aceptar\" style=\"width:60px\" onclick=\"dialog.close();sendAcceptPrompt('" + callback_comp + "');return false;\"></td>"
	html +="</tr>"
	html +="</table>"
	dialog.setHtmlContent(html);
	dialog.display();
}

function sendAccept(callback_comp) {
	enqueueUpdate(new Update("event","accept"));
    sendUpdate(new Update("event_target", callback_comp));
}

function sendCancel(callback_comp) {
	enqueueUpdate(new Update("event","cancel"));
    sendUpdate(new Update("event_target", callback_comp));
}

function sendAcceptPrompt(callback_comp) {
	var	input = document.getElementById(callback_comp + ":prompt_input");
	enqueueChange(input, inputGetValue);
	enqueueUpdate(new Update("event","accept"));
    sendUpdate(new Update("event_target", callback_comp));
}

function openErrorDialog(message, callback_comp, pwb_url) {
	dialog = new DHTML_modalMessage();
	dialog.setShadowOffset(5);
	dialog.setSize(400,150);
	dialog.setShadowDivVisible(true);
	html = ""
	html += "<table width=\"100%\" border=\"0\">"
	html +=	"<tr>"
	html +="<td rowspan=\"2\"><img src=\"" + pwb_url + "/DefaultCMS/Templates/Default/icons/Neu/48x48/status/dialog-error.png\"></td>"
	html += "<td><h3>" + message + "</h3></td>"
	html += "</tr>"
	html +="<tr>"
	html +="<td align=\"right\"><input type=\"button\" value=\"Aceptar\" style=\"width:60px\" onclick=\"dialog.close();sendAccept('" + callback_comp + "');return false;\"></td>"
	html +="</tr>"
	html +="</table>"
	dialog.setHtmlContent(html);
	dialog.display();
}

function openQuestionDialog(message, callback_comp, pwb_url) {
	dialog = new DHTML_modalMessage();
	dialog.setShadowOffset(5);
	dialog.setSize(400,150);
	dialog.setShadowDivVisible(true);
	html = ""
	html += "<table width=\"100%\" border=\"0\">"
	html +=	"<tr>"
	html +="<td rowspan=\"2\"><img src=\"" + pwb_url + "/DefaultCMS/Templates/Default/icons/Neu/48x48/status/dialog-question.png\"></td>"
	html += "<td><h3>" + message + "</h3></td>"
	html += "</tr>"
	html +="<tr>"
	html +="<td align=\"right\"><input type=\"button\" value=\"Si\" style=\"width:60px\" onclick=\"dialog.close();sendYes('" + callback_comp + "');return false;\"><input type=\"button\" value=\"No\" style=\"width:60px\" onclick=\"dialog.close();sendNo('" + callback_comp + "');return false;\"></td>"
	html +="</tr>"
	html +="</table>"
	dialog.setHtmlContent(html);
	dialog.display();
}

function sendYes(callback_comp) {
	enqueueUpdate(new Update("event","yes"));
    sendUpdate(new Update("event_target", callback_comp));
}

function sendNo(callback_comp) {
	enqueueUpdate(new Update("event","no"));
    sendUpdate(new Update("event_target", callback_comp));
}

