import {crearModal} from "./modal.js"

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
            <label for="horaInicio">Hora de inicio</label>
            <input type="time" id="horaInicio" min="08:00" max="22:00" value=${botonEditar.parentNode.parentNode.childNodes[7].outerText}>
            <input type="time" hidden id="horaInicioOriginal" value=${botonEditar.parentNode.parentNode.childNodes[7].outerText}>
            <label for="horaFin">Hora de fin</label>
            <input type="time" id="horaFin" min="08:30" max="23:00" value=${botonEditar.parentNode.parentNode.childNodes[9].outerText}>
            <input type="text" hidden id="pista" value=${botonEditar.parentNode.parentNode.childNodes[1].outerText}>
            <label for="informacion">Información de la reserva</label>
            <input type="text" id="informacion" value=${botonEditar.parentNode.parentNode.childNodes[13].outerText}>
        </form>
    `);

    // Solo creamos el botón de Borrar la primera vez que pulsamos en Editar
    if(document.getElementsByClassName('btn-danger')[0] == null) {
        const modalPie = document.getElementsByClassName('modal-footer')[0];
        modalPie.insertAdjacentHTML('afterbegin', `
            <button type="button" class="btn btn-danger">Borrar</button>
        `);
    }

    const modal = new bootstrap.Modal('#modal');
    modal.show();

    actualizarReserva();
    borrarReserva();
}

async function actualizarCalendario(datosAEnviar, boton) {
    const formData = new FormData();

    formData.append(boton, datosAEnviar);

    await fetch('actualizarCalendario.php', {
        method: 'post',
        body: formData
    }).then ((response) => response.text()
    ).then(function (data) {
        location.reload();
    }).catch(function (err) {
        console.log("Ha habido un error");
    });
}

// Se modifica una reserva
function actualizarReserva() {
    const botonEditar = document.querySelectorAll('.modal-footer .btn-primary')[0];
    $(botonEditar).on('click', function(event) {
        let datosAEnviar = JSON.stringify({  
            fecha: document.getElementById("fecha").value,
            fechaOriginal: document.getElementById("fechaOriginal").value,
            horaInicio: document.getElementById("horaInicio").value + ":00", 
            horaInicioOriginal: document.getElementById("horaInicioOriginal").value, 
            horaFin: document.getElementById("horaFin").value + ":00",
            pista: document.getElementById("pista").value,
            informacion: document.getElementById("informacion").value
        });

        actualizarCalendario(datosAEnviar, botonEditar.outerText);

        event.stopPropagation();
    });
}

// Se borra una reserva
function borrarReserva() {
    const botonBorrar = document.querySelectorAll('.modal-footer .btn-danger')[0];
    $(botonBorrar).on('click', function(event) {
        let datosAEnviar = JSON.stringify({  
            fecha: document.getElementById("fechaOriginal").value,
            horaInicio: document.getElementById("horaInicioOriginal").value, 
            pista: document.getElementById("pista").value,
        });

        actualizarCalendario(datosAEnviar, botonBorrar.outerText);

        event.stopPropagation();
    });
}