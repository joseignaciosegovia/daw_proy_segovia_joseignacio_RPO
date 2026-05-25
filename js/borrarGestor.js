import { crearModalConfirmacion } from './modal.js';

document.addEventListener('DOMContentLoaded', () => {
    const botonBorrar = document.querySelector('button[name="Borrar"]');

    if (botonBorrar) {
        botonBorrar.addEventListener('click', (event) => {
            // Evitamos que el formulario se envíe directamente
            event.preventDefault();

            const formularioBorrar = botonBorrar.closest('form');

            crearModalConfirmacion(
                () => confirmarBorrado(document.getElementById("email").value),
                '¿Deseas borrar este gestor? Esta acción no se puede deshacer.'
            );
        });
    }
});

// Función que define el comportamiento del botón de confirmar la cancelación
async function confirmarBorrado(pista) {
    // URL del servidor
    const BASE_URL = window.location.origin;

    let url = new URL(BASE_URL + '/servidor/editarGestor.php');
    let parametro = {Borrar: pista};
    url.search = new URLSearchParams(parametro).toString();

            fetch(url, {
                method: 'get'
            }).then ((response) => response.text()
            ).then(function (reservas) {
                location.replace("/servidor/administrarGestores.php");
                
            }).catch(function (err) {
                console.log("Ha habido un error");
            });
}