// Hay que importar "esLocale" para arreglar un bug con el idioma
import esLocale from '../node_modules/@fullcalendar/core/locales/es.js';
import {crearModal, cerrarModal} from "./modal.js"

document.addEventListener('DOMContentLoaded', function() {
    cargarCalendario();
});

// Función que muestra el calendario
function cargarCalendario(){

    var calendario = JSON.parse(document.getElementById('calendario').outerText);

    // Sacamos la pista del array que contenía el calendario y el nombre de la pista
    const pista = calendario.pop();

    var calendarEl = document.getElementById('calendario');
    // Borramos el contenido del div para que no muestre la información de la pista y las fechas que ya hemos recogido
    calendarEl.replaceChildren();
    calendarEl.hidden = false;

    crearModal();

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',

        firstDay: 1,

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek"
        },
        
        locale: esLocale,

        dayMaxEvents: true, 

        allDaySlot: false,

        // No mostrar sábados y domingos
        hiddenDays: [6, 0],

        slotMinTime: "08:00:00",

        slotMaxTime: "22:00:00",

        // Al pinchar en el calendario, mostraremos un modal para crear un evento
        dateClick: function(info) {
            const modalCuerpo = document.getElementsByClassName('modal-body')[0];

            const fechaCompleta = info.date;
            
            const fecha = info.dateStr.substring(0, info.dateStr.indexOf('T'));
            const horaInicio = info.dateStr.substring(info.dateStr.indexOf('T') +1, info.dateStr.indexOf('+'));

            const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;
            const horaReserva = fechaCompleta.getTime() / 1000 / 60 / 60;

            const modalBotonConfirmar = document.getElementsByClassName('modal-footer')[0].getElementsByClassName('btn-primary')[0];
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

                modalBotonConfirmar.hidden = true;
                modal.show();
            }

            else {
                document.getElementsByClassName('modal-title')[0].innerHTML = "Horario reservado el " + fecha + " a las " + horaInicio;
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
                
                confirmarFecha(pista, calendar, modal);
            }
            
            cerrarModal(modal);
            
        }
    });

    calendar.render();

    var events = new Array();
    var editable;

    // Rellenamos el array de eventos con las fechas ocupadas para la pista
    for(let fecha of calendario) {
        // El administrador solo podrá modificar un horario ocupado que no haya sido fruto de una reserva de un cliente
        if(fecha.informacion == "Reserva realizada por un cliente") {
            editable = false;
        }
        else {
            editable = true;
        }

        events.push({
            title: fecha.informacion,
            start: fecha.fecha + "T" + fecha.horaInicio,
            end: fecha.fecha + "T" + fecha.horaFin,
            editable: editable
        })
    }

    // Indicamos los eventos para el calendario
    calendar.setOption('events', events);
}

function confirmarFecha(pista, calendar, modal) {
    const botonConfirmar = $('.modal-footer .btn-primary');
    $(botonConfirmar[0]).on('click', async function(event) {

        const informacion = document.getElementById("informacion").value;
        const fecha = document.getElementById("fecha").value;
        const horaInicio = document.getElementById("horaInicio").value;
        const horaFin = document.getElementById("horaFin").value + ":00";

        let datosAEnviar = JSON.stringify({  
            fecha: fecha,
            horaInicio: horaInicio, 
            horaFin: horaFin,
            pista: pista,
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
            location.reload();
        }).catch(function (err) {
            console.log("Ha habido un error");
        });

        event.stopPropagation();
    });
}