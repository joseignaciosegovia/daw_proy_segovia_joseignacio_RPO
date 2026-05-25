import {crearModal, cerrarModal, crearModalConfirmacion} from "./modal.js"

document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('borrar') != null) {
        // Si pulsamos el botón de borrar pista
        document.getElementById('borrar').addEventListener('click', function(e) {
            // Evitamos que el formulario se envíe al pulsar el botón
            e.preventDefault();

            crearModalConfirmacion(() => {
                // Al confirmar, redirigimos con el parámetro Borrar para que PHP gestione el borrado
                const url = new URL(window.location.origin + '/servidor/editarPista.php');
                url.searchParams.set('Borrar', document.getElementById("id").value);
                window.location.href = url.toString();
                }, 
                "¿Deseas borrar la pista?"
            );
        });
    }

    // Función que define el comportamiento del botón de confirmar la cancelación
    async function confirmarCancelacion(pista) {
        const botonConfirmar = $('.modal-footer .btn-danger');
        // Pinchamos en el botón de Confirmar
        $(botonConfirmar[0]).on('click', function(event) {
            // URL del servidor
            const BASE_URL = window.location.origin;

            let url = new URL(BASE_URL + '/servidor/editarPista.php');
            let parametro = {Borrar: pista};
            url.search = new URLSearchParams(parametro).toString();

            fetch(url, {
                method: 'get'
            }).then ((response) => response.json()
            ).then(function (reservas) {
                
                
            }).catch(function (err) {
                console.log("Ha habido un error");
            });
    
            event.stopPropagation();
        });
    }
});