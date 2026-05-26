// Hay que importar "esLocale" para arreglar un bug con el idioma
import esLocale from '../node_modules/@fullcalendar/core/locales/es.js';
import {crearModal, cerrarModal} from "./modal.js"

document.addEventListener('DOMContentLoaded', function() {
    cargarCalendario();
});

// Función que muestra el calendario
function cargarCalendario(){

    var calendario = JSON.parse(document.getElementById('calendario').outerText);

    // Sacamos el nombre de la pista del array que contenía el calendario, el id de la pista y su nombre
    const pista = calendario.pop();
    // Sacamos el id de la pista del array que contenía el calendario, el id de la pista y su nombre
    const id = calendario.pop();
    // Guardamos en una variable el div en el que incluiremos el calendario
    var calendarEl = document.getElementById('calendario');
    // Borramos el contenido del div para que no muestre la información de la pista y las fechas que ya hemos recogido
    calendarEl.replaceChildren();
    // Añadimos el título de la pista seleccionada y la leyenda del calendario
    calendarEl.insertAdjacentHTML('beforebegin', `
        <div class="d-flex justify-content-center" id="leyenda"><strong>Leyenda:</strong>&ensp;&ensp;
            <span><span class="color" style="background:#4EB272;"></span>Disponible</span>
            <span><span class="color" style="background:#3788D8;"></span>Ocupado</span>
        </div>
    `);
    calendarEl.hidden = false;

    crearModal();

    var calendar = new FullCalendar.Calendar(calendarEl, {
        forceEventDuration: true,
        initialView: 'timeGridWeek',

        firstDay: 1,
        // Ponemos el calendario en español

        locale: esLocale,

        // No mostramos sábados y domingos
        hiddenDays: [6, 0],

        slotMinTime: "08:00:00",

        slotMaxTime: "22:00:00",

        allDaySlot: false,

        // La altura del calendario se ajusta automáticamente a su tamaño
        height: 'auto',

        // Formato de la columna que indica la hora
        slotLabelFormat:{
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
            meridiem: 'short',
        },

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek"
        },  

        dayMaxEvents: true, 

        // Al pinchar en el calendario, mostraremos un modal para añadir un horario ocupado
        dateClick: function(info) {
            const modalCuerpo = document.getElementsByClassName('modal-body')[0];

            const fechaCompleta = info.date;
            
            const fecha = info.dateStr.substring(0, info.dateStr.indexOf('T'));
            const horaInicio = info.dateStr.substring(info.dateStr.indexOf('T') +1, info.dateStr.indexOf('+'));

            const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;
            const horaReserva = fechaCompleta.getTime() / 1000 / 60 / 60;

            const modalBotonConfirmar = document.getElementsByClassName('modal-footer')[0].getElementsByClassName('btn-success')[0];
            // Borramos el cuerpo del modal para que no muestre el mensaje anterior
            modalCuerpo.replaceChildren();

            const modal = new bootstrap.Modal('#modal');

            // Si se intenta hacer una reserva de una fecha que ya ha pasado
            if(horaActual > horaReserva) {
                document.getElementsByClassName('modal-title')[0].innerHTML = "No se puede añadir la reserva";
                // Mostramos el mensaje indicando que no se puede añadir una reserva en un horario pasado
                modalCuerpo.insertAdjacentHTML('afterbegin', `
                    No se puede hacer una reserva de una fecha pasada
                `);
                // Ocultamos el botón de confirmar reserva (porque no se va a poder hacer una)
                modalBotonConfirmar.hidden = true;
                modal.show();
            }

            else {
                document.getElementsByClassName('modal-title')[0].innerHTML = "Reservar horario el " + fecha + " a las " + horaInicio;
                // Mostramos el botón de confirmar reserva
                modalBotonConfirmar.hidden = false;
                // Mostramos el mensaje indicando que se va a añadir un horario ocupado
                modalCuerpo.insertAdjacentHTML('afterbegin', `
                    <label for="horaFin">Indique la hora de fin</label>
                    <input type="text" hidden id="fecha" value=${fecha}>
                    <input type="text" hidden id="horaInicio" value="${horaInicio}">
                    <input type="time" id="horaFin" name="horaFin"></br></br>
                    <label for="informacion">Información sobre la reserva</label>
                    <textarea id="informacion" rows="5" cols="50"></textarea>
                `);
                modal.show();
                
                confirmarFecha(id);
            }
            
            cerrarModal(modal);
            
        },
        // Soltamos una reserva después de arrastrarla
        eventDrop: function(info) {
            const evento = info.event;
            const eventoAnterior = info.oldEvent;
            const delta = info.delta; // Diferencia de tiempo respecto a la posición original

            const idReserva = info.event.id;
            const nuevaFecha = evento.startStr.substring(0, evento.startStr.indexOf('T'));
            const nuevaHoraInicio = evento.startStr.substring(evento.startStr.indexOf('T') + 1, evento.startStr.indexOf('+'));
            const nuevaHoraFin = evento.endStr.substring(evento.endStr.indexOf('T') + 1, evento.endStr.indexOf('+'));

            const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;
            const horaNueva = Date.parse(new Date(evento.startStr)) / 1000 / 60 / 60;

            // Si hemos soltado la reserva en una posición diferente
            if(delta != 0) {
                // Si se intenta mover una reserva a una fecha pasada
                if(horaActual > horaNueva) {
                    const modal = new bootstrap.Modal('#modal');
                    const modalCuerpo = document.getElementsByClassName('modal-body')[0];
                    // Borramos el cuerpo para no mostrarlo varias veces cada vez que se mueva una reserva
                    modalCuerpo.replaceChildren();
                    document.getElementsByClassName('modal-title')[0].innerHTML = "No se puede mover la reserva";
                    // Mostramos el mensaje indicando que no se puede mover una reserva a un horario pasado
                    modalCuerpo.insertAdjacentHTML('afterbegin', `
                        No se puede mover una reserva a una fecha pasada
                    `);
                    // Ocultamos el botón de confirmar cambio (porque no se va a poder hacer)
                    const modalBotonConfirmar = document.getElementsByClassName('modal-footer')[0].getElementsByClassName('btn-success')[0];
                    modalBotonConfirmar.hidden = true;
                    modal.show();
                    // Revertimos la situación para dejar la reserva donde estaba
                    info.revert();
                }
                // Si se intenta mover una reserva a una fecha pasada
                else {
                    const modal = new bootstrap.Modal('#modal');
                    const modalCuerpo = document.getElementsByClassName('modal-body')[0];
                    // Borramos el cuerpo para no mostrarlo varias veces cada vez que se mueva una reserva
                    modalCuerpo.replaceChildren();
                    document.getElementsByClassName('modal-title')[0].innerHTML = "Mover la reserva a una nueva fecha";
                    // Mostramos el mensaje indicando que se va a mover una reserva, indicando el nuevo horario
                    modalCuerpo.insertAdjacentHTML('afterbegin', `
                        Va a mover una reserva a ${nuevaFecha} ${nuevaHoraInicio}
                    `);
                    // Movemos el botón de confirmar cambio
                    const modalBotonConfirmar = document.getElementsByClassName('modal-footer')[0].getElementsByClassName('btn-success')[0];
                    modalBotonConfirmar.hidden = false;
                    modal.show();
                    // Actualizamos la base de datos
                    confirmarMoverFecha(idReserva, nuevaFecha, nuevaHoraInicio, nuevaHoraFin, info);
                }
            }
        }
    });

    calendar.render();

    var events = new Array();
    var editable;
    const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;

    // Rellenamos el array de eventos con las reservas ocupadas para la pista
    for(let reserva of calendario) {
        // Guardamos la hora de cada evento para comprobar si es de una fecha pasada o no
        const horaEvento = Date.parse(new Date(reserva.fecha + "T" + reserva.horaInicio)) / 1000 / 60 / 60;
        // El administrador solo podrá modificar un horario ocupado que no sea de una fecha pasada
        if(horaActual > horaEvento) {
            editable = false;
        }
        else {
            editable = true;
        }

        events.push({
            id: reserva.id,
            title: reserva.informacion,
            start: reserva.fecha + "T" + reserva.horaInicio,
            end: reserva.fecha + "T" + reserva.horaFin,
            editable: editable,
            borderColor: '#285B8D'
        })
    }

    // Añadimos los eventos en el calendario
    calendar.setOption('events', events);
}

// Función que define lo que pasará cuando se confirme una reserva
function confirmarFecha(id) {
    const botonConfirmar = $('.modal-footer .btn-success');
    // Comportamiento del botón de Confirmar
    $(botonConfirmar[0]).on('click', async function(event) {

        const informacion = document.getElementById("informacion").value;
        const fecha = document.getElementById("fecha").value;
        const horaInicio = document.getElementById("horaInicio").value;
        const horaFin = document.getElementById("horaFin").value + ":00";

        let datosAEnviar = JSON.stringify({  
            fecha: fecha,
            horaInicio: horaInicio, 
            horaFin: horaFin,
            id: id,
            informacion: informacion
        });

        const formData = new FormData();

        // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

        formData.append("datos", datosAEnviar);

        await fetch('actualizarCalendario.php', {
            method: 'post',
            body: formData
        }).then ((response) => response.text()
        ).then(function (data) {
            // Actualizamos la página para mostrar el calendario con el nuevo horario añadido
            location.reload();
        }).catch(function (err) {
            console.log("Ha habido un error");
        });

        event.stopPropagation();
    });
}
// Función que define lo que pasará cuando se mueva una reserva a otra fecha
function confirmarMoverFecha(idReserva, nuevaFecha, nuevaHoraInicio, nuevaHoraFin, info) {
    const botonConfirmar = $('.modal-footer .btn-success');
    const botonCancelar = $('.modal-footer .btn-secondary');
    // Comportamiento del botón de Confirmar
    $(botonConfirmar[0]).on('click', async function(event) {
        let datosAEnviar = JSON.stringify({  
            fecha: nuevaFecha,
            horaInicio: nuevaHoraInicio, 
            horaFin: nuevaHoraFin,
            id: idReserva
        });

        const formData = new FormData();

        // Al llamar "Confirmar" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['Confirmar']"

        formData.append("Confirmar", datosAEnviar);

        await fetch('actualizarCalendario.php', {
            method: 'post',
            body: formData
        }).then ((response) => response.text()
        ).then(function (data) {
            // Actualizamos la página para mostrar el calendario con el nuevo horario modificado
            location.reload();
        }).catch(function (err) {
            console.log("Ha habido un error");
        });

        event.stopPropagation();
    });
    // Comportamiento del botón de Cancelar
    $(botonCancelar[0]).on('click', async function(event) {
        // Revertimos la situación para dejar la reserva donde estaba
        info.revert();
    });
}