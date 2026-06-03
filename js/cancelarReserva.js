import {crearModal, cerrarModal} from "./modal.js"

document.addEventListener('DOMContentLoaded', function() {

    // Recorremos todos los botones de cancelar
    for (const botonCancelar of document.querySelectorAll('.cancelarReserva')) {
        // Cada vez que pinchamos en uno de los botones de cancelar, cancelamos la reserva asociada
        $(botonCancelar).on('click', function(){
            cancelarReserva(botonCancelar);
        });
    }
});

// Función para cancelar una reserva desde el cliente
function cancelarReserva(botonCancelar) {
    crearModal();
    // Obtenemos la información de la reserva que vamos a cancelar
    const fecha = botonCancelar.parentNode.parentNode.childNodes[0].textContent;
    const horaInicio = botonCancelar.parentNode.parentNode.childNodes[1].textContent;
    const pista = botonCancelar.parentNode.parentNode.childNodes[3].textContent;

    const modalTitulo = document.getElementsByClassName('modal-title')[0];
    // Borramos el título para no mostrarlo varias veces cada vez que se pulse el botón de cancelar reserva
    modalTitulo.replaceChildren();
    modalTitulo.insertAdjacentHTML('afterbegin', `
        Eliminar reserva
    `);

    const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;
    // La hora actual teniendo en cuenta la diferencia de franja horaria
    const horaReserva = (Date.parse(fecha) / 1000 / 60 / 60) + (Number(horaInicio.split(":")[0])) + (new Date().getTimezoneOffset() / 60);

    // Mostramos el mensaje indicando que se va a eliminar una reserva
    const modalCuerpo = document.getElementsByClassName('modal-body')[0];
    // Borramos el cuerpo del modal para que no muestre el mensaje anterior
    modalCuerpo.replaceChildren();

    var devolverDinero;

    // Si quedan más de 12 horas hasta la hora de la reserva, se devuelve el dinero
    if(horaReserva >= (horaActual+12)) {
        modalCuerpo.insertAdjacentHTML('afterbegin', `
            <br><br>Se devolverá el dinero
        `);
        devolverDinero = true;
    }

    else {
        modalCuerpo.insertAdjacentHTML('afterbegin', `
           <br><br>No se devolverá el dinero porque quedan menos de 12 horas 
        `);
        devolverDinero = false;
    }

    modalCuerpo.insertAdjacentHTML('afterbegin', `
        ¿Desea eliminar la reserva del ${fecha} a las ${horaInicio} en la pista ${pista}?
    `);

    const modal = new bootstrap.Modal('#modal');
    // Ponemos el botón de confirmar del modal en rojo
    document.getElementsByClassName("btn-success")[0].setAttribute("class", "btn btn-danger");
    modal.show();

    cerrarModal(modal);
    confirmarCancelacion(parseInt(botonCancelar.dataset.id), devolverDinero);
}

// Función que define el comportamiento del botón de confirmar la cancelación
function confirmarCancelacion(id, devolverDinero) {
    const botonConfirmar = $('.modal-footer .btn-danger');
    // Pinchamos en el botón de Confirmar
    $(botonConfirmar[0]).on('click', function(event) {
        let datosAEnviar = JSON.stringify({  
            id: id,
            devolverDinero: devolverDinero
        });

        const formData = new FormData();

        // Al llamar "cancelar" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['cancelar']"

        formData.append("cancelar", datosAEnviar);

        fetch('../servidor/actualizarCalendario.php', {
            method: 'post',
            body: formData
        }).then ((response) => response.text()
        ).then(function (datos) {
            // Después de actualizar el calendario nos dirigimos a reservasCliente.php para que el cliente vea sus reservas
            const direccion = "/public/reservasCliente.php";
            location.replace(direccion);

        }).catch(function (err) {
            console.log("Ha habido un error");
        });
  
        event.stopPropagation();
    });
}