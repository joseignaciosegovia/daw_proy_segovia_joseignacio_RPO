document.addEventListener('DOMContentLoaded', function() {

    // Recorremos todos los botones de "Editar"
    for (const botonEditar of document.querySelectorAll('.editarPista')) {
        // Cada vez que pinchamos en uno de los botones de "Editar", editamos la reserva asociada
        $(botonEditar).on('click', function(){
            editarReserva(botonEditar);
        });
    }
});

function editarReserva(botonEditar) {
    crearModal();

    const modalTitulo = document.getElementsByClassName('modal-title')[0];
    modalTitulo.insertAdjacentHTML('afterbegin', `
        Editar reserva
    `);

    // Mostramos el mensaje indicando que se va a editar una reserva
    const modalCuerpo = document.getElementsByClassName('modal-body')[0];
    // Borramos el cuerpo del modal para que no muestre el mensaje anterior
    modalCuerpo.replaceChildren();

    modalCuerpo.insertAdjacentHTML('afterbegin', `
        <form class="row needs-validation px-4" name="editarReserva" novalidate>
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" value=${botonEditar.parentNode.parentNode.childNodes[5].outerText}>
            <input type="date" hidden id="fechaOriginal" value=${botonEditar.parentNode.parentNode.childNodes[5].outerText}>
            <label for="horaInicio"Hora de inicio</label>
            <input type="time" id="horaInicio" min="08:00" max="22:00" value=${botonEditar.parentNode.parentNode.childNodes[7].outerText}>
            <input type="time" hidden id="horaInicioOriginal" value=${botonEditar.parentNode.parentNode.childNodes[7].outerText}>
            <label for="horaFin">Hora de fin</label>
            <input type="time" id="horaFin" min="08:30" max="23:00" value=${botonEditar.parentNode.parentNode.childNodes[9].outerText}>
            <input type="time" hidden id="horaFinOriginal" value=${botonEditar.parentNode.parentNode.childNodes[9].outerText}>
            <label for="informacion">Información de la reserva</label>
            <input type="text" id="informacion" value=${botonEditar.parentNode.parentNode.childNodes[13].outerText}>
        </form>
    `);

    const modal = new bootstrap.Modal('#modal');
    modal.show();

    confirmarEdicion(modal);
}

function crearModal() {
    // Creamos el modal a continuación del pie
    const footer = document.getElementsByTagName('footer')[0];
    $(footer).append(`
        <div class="modal" id="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Confirmar</button>
                </div>
                </div>
            </div>
        </div>
    `);
}

function cerrarModal(modal) {
    const botonCerrar = $('.modal-footer .btn-secondary');
    $(botonCerrar[0]).on('click', function(event) {
  
        // Ocultamos el modal
        modal.hide();
  
        event.stopPropagation();
    });
}

function confirmarEdicion(modal) {
    const botonConfirmar = $('.modal-footer .btn-primary');
    $(botonConfirmar[0]).on('click', async function(event) {
        let datosAEnviar = JSON.stringify({  
            fecha: document.getElementById("fecha").value,
            fechaOriginal: document.getElementById("fechaOriginal").value,
            horaInicio: document.getElementById("horaInicio").value + ":00", 
            horaInicioOriginal: document.getElementById("horaInicioOriginal").value, 
            horaFin: document.getElementById("horaFin").value + ":00",
            horaFinOriginal: document.getElementById("horaFinOriginal").value,
            informacion: document.getElementById("informacion").value
        });

        const formData = new FormData();

        // Al llamar "editar" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['editar']"

        formData.append("editar", datosAEnviar);

        await fetch('actualizarCalendario.php', {
            method: 'post',
            body: formData
        }).then ((response) => response.text()
        ).then(function (data) {
            location.reload();
        }).catch(function (err) {
            console.log("Ha habido un error");
        });

        event.stopPropagation();
    });
}