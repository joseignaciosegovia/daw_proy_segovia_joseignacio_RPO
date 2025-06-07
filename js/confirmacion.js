document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('borrar') != null) {
        // Si pulsamos el botón de borrar pista
        document.getElementById('borrar').addEventListener('click', function(e) {
            e.preventDefault();
            pregunta("¿Seguro que quieres borrar esta pista?", "http://localhost/proyecto/servidor/editarPista.php?Borrar=" + document.getElementById('nombreOriginal').value)
        });
    }
});

// Función que muestra la pregunta antes de confirmar una acción
function pregunta(mensaje, direccion) {
    if (confirm(mensaje)) {
        location.replace(direccion);
    }
}