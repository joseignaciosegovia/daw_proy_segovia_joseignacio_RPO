document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('borrar') != null) {
        document.getElementById('borrar').addEventListener('click', function(e) {
            e.preventDefault();
            pregunta("¿Seguro que quieres borrar esta pista?", "http://localhost/proyecto/servidor/editarPista.php?Borrar=" + document.getElementById('nombreOriginal').value)
        });
    }
});

function pregunta(mensaje, direccion) {
  if (confirm(mensaje)) {
    location.replace(direccion);
  }
}