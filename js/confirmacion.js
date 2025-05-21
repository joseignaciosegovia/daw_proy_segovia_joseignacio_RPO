document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('borrar').addEventListener('click', function(e) {
    e.preventDefault();
    pregunta()
  });
});

function pregunta() {
  if (confirm('¿Seguro que quieres borrar esta pista?')) {
    location.replace("http://localhost/proyecto/servidor/editarPista.php?Borrar=" + document.getElementById('nombreOriginal').value);
  }
}