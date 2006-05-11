var loading = 0;
var loader = document.createElement("div");
t = document.createTextNode('');
loader.setAttribute("id", "loader");
loader.appendChild(t);
app = document.getElementById('app');
app.appendChild(loader);

function loadingStart() {
        loading++;
        loader.style.visibility="visible";
        loader.replaceChild(document.createTextNode(loading +" actions remaining..."),loader.firstChild);
}

function loadingStop() {
        loading--;
        loader.replaceChild(document.createTextNode(loading +" actions remaining..."),loader.firstChild);
        if (loading == 0) {
            loader.style.visibility="hidden";
        }
}

function ajaxError() {
    // Nota: para que esta funcion no sea invocada, retornar true en la funcion handler.
    // Para que deje de joder, documentar el siguiente alert
    alert("Hubo un error. Por favor, intente nuevamente");
}