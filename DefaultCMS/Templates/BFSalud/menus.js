function showMenu(elem){
    var tab = elem.parentNode.nextSibling.nextSibling;
    tab.style.visibility='visible';
}
function hideMenu(elem){
    var tab = elem.parentNode.nextSibling.nextSibling;
    f = function () {
        tab.style.visibility='hidden';
	    clearTimeout(tab.timeoutMenu);
        tab.timeoutMenu = null;
    }
    tab.timeoutMenu = setTimeout('f()', 300);
}

function showMenuElem(elem){
    var tab = elem;
    while(tab.tagName!="TABLE"){
        tab = tab.parentNode;
    }
    clearTimeout(tab.timeoutMenu);
    tab.timeoutMenu = null;
}
function hideMenuElem(elem){
    var tab = elem;
    while(tab.tagName!="TABLE"){
        tab = tab.parentNode;
    }
    f = function () {
        tab.style.visibility='hidden';
        clearTimeout(tab.timeoutMenu);
        tab.timeoutMenu = null;
    }
    tab.timeoutMenu = setTimeout('f()', 300);
}