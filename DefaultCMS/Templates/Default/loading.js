var loader = document.createElement("div");

function initialize_loading(){
	t = document.createTextNode('');
	loader.setAttribute("id", "loader");
	loader.onclick = cancel_ajax;
	loader.appendChild(t);
	var ap = document.getElementById('app');
	ap.appendChild(loader);
}

addOnLoad(initialize_loading);

function loadingStart(count) {
        loader.style.visibility="visible";
        loader.replaceChild(document.createTextNode(count +" actions remaining..."),loader.firstChild);
}

function loadingStop(count) {
        loader.replaceChild(document.createTextNode(count +" actions remaining..."),loader.firstChild);
        if (count == 0) {
            loader.style.visibility="hidden";
        }
}

function ajaxError() {
    // Nota: para que esta funcion no sea invocada, retornar true en la funcion handler.
    // Para que deje de joder, documentar el siguiente alert
    alert("Hubo un error. Por favor, intente nuevamente");
}