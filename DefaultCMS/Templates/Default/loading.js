var loading = 0;
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
        loading++;
        loader.style.visibility="visible";
        loader.replaceChild(document.createTextNode(loading +" actions remaining..."),loader.firstChild);
}

function loadingStop() {
        loading--;
        loader.replaceChild(document.createTextNode(loading +" actions remaining..."),loader.firstChild);
        if (loading <= 0) {
            loader.style.visibility="hidden";
            loading=0;
        }
}

function ajaxError() {
    // Nota: para que esta funcion no sea invocada, retornar true en la funcion handler.
    // Para que deje de joder, documentar el siguiente alert
    alert("Hubo un error. Por favor, intente nuevamente");
}