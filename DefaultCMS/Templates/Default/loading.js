var loader = document.createElement("div");

function initialize_loading(){
t = document.createTextNode('');
loader.setAttribute("id", "loader");
loader.onclick = cancel_ajax;
loader.appendChild(t);
ap = document.getElementById('app');
ap.appendChild(loader);
}

window.onload=initialize_loading;

function loadingStart() {
        loader.style.visibility="visible";
        loader.replaceChild(document.createTextNode(ajax_queue.length +" actions remaining..."),loader.firstChild);
}

function loadingStop() {
        loader.replaceChild(document.createTextNode(ajax_queue.length +" actions remaining..."),loader.firstChild);
        if (ajax_queue.length == 0) {
            loader.style.visibility="hidden";
        }
}

function ajaxError() {
    // Nota: para que esta funcion no sea invocada, retornar true en la funcion handler.
    // Para que deje de joder, documentar el siguiente alert
    alert("Hubo un error. Por favor, intente nuevamente");
}