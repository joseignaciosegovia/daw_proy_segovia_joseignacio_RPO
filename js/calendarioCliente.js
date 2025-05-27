// Hay que importar "esLocale" para arreglar un bug con el idioma
import esLocale from '../node_modules/@fullcalendar/core/locales/es.js';

document.addEventListener('DOMContentLoaded', function() {
    
    // Mostrar calendario para el cliente cada vez que pinche en una pista para reservarla
    for (const pista of document.querySelectorAll('.accordion-body')) {
        $(pista).on('click', async function(){

            cargarCalendario(pista.outerText);
        });
    }
});

// Función que muestra el calendario
async function cargarCalendario(pista){

    crearModal();
    // Obtenemos el email del cliente del párrafo
    const cliente = document.getElementById('cliente').outerText;

    var calendarEl = document.getElementById('calendario');
    // Indicamos la pista antes del calendario
    var tituloPista = document.getElementById('tituloPista');
    tituloPista.innerHTML = "Pista: " + pista;
    calendarEl.before(tituloPista);
    // Borramos el contenido del div para que no muestre la información de la pista y las fechas que ya hemos recogido
    calendarEl.replaceChildren();

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',

        firstDay: 1,

        locale: esLocale,

        // Solo se muestran horas enteras
        slotDuration: "01:00:00",

        // No mostrar sábados y domingos
        hiddenDays: [ 6, 0 ],

        slotMinTime: "08:00:00",

        slotMaxTime: "22:00:00",

        allDaySlot: false,

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay"
        },
        dayMaxEvents: true, 

        // Al pinchar en el calendario, mostraremos un modal para crear un evento
        dateClick: function(info) {
            const fechaCompleta = info.date;
            
            const fecha = info.dateStr.substring(0, info.dateStr.indexOf('T'));
            const horaInicio = fechaCompleta.getHours() + ":00:00";
            const horaFin = (fechaCompleta.getHours() +1) + ":00:00";

            const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;
            const horaReserva = fechaCompleta.getTime() / 1000 / 60 / 60;
            
            const modalBotonConfirmar = document.getElementsByClassName('modal-footer')[0].getElementsByClassName('btn-primary')[0];
            const modalCuerpo = document.getElementsByClassName('modal-body')[0];
            // Borramos el cuerpo del modal para que no muestre el mensaje anterior
            modalCuerpo.replaceChildren();

            // Si se intenta hacer una reserva de una fecha que ya ha pasado
            if(horaActual > horaReserva) {
                document.getElementsByClassName('modal-title')[0].innerHTML = "No se puede añadir la reserva";
                // Mostramos el mensaje indicando que no se puede añadir una reserva en un horario pasado
                modalCuerpo.insertAdjacentHTML('afterbegin', `
                    No se puede hacer una reserva de una fecha pasada
                `);

                modalBotonConfirmar.hidden = true;
            }

            else {
                document.getElementsByClassName('modal-title')[0].innerHTML = "Añadir reserva";
                modalBotonConfirmar.hidden = false;
                // Mostramos el mensaje indicando que se va a añadir un horario ocupado
                modalCuerpo.insertAdjacentHTML('afterbegin', `
                    Añadir una reserva el ${fecha} a las ${horaInicio}
                `);

                confirmarFecha(fecha, horaInicio, horaFin, pista, cliente);
            }
            
            const modal = new bootstrap.Modal('#evento');
            modal.show();

            cerrarModal(modal);
        }
    });

    calendar.render();

    var events = new Array();

    let url = new URL('http://localhost/proyecto/servidor/obtenerCalendario.php');
    let parametro = {pista: pista};
    url.search = new URLSearchParams(parametro).toString();

    // Obtenemos las fechas ocupadas de esta pista

    await fetch(url, {
        method: 'get'
    }).then ((response) => response.json()
    ).then(function (reservas) {
        
        for(const reserva of reservas){
            // Rellenamos los horarios ocupados
            // CORREGIR LA HORA DE end
            events.push({
                start: reserva.fecha + "T" + reserva.horaInicio,
                end: reserva.fecha + "T" + reserva.horaFin,
                backgroundColor: "red",
                borderColor: "red"
            });
        }
    }).catch(function (err) {
        console.log("Ha habido un error");
    });

    // Indicamos los eventos para el calendario
    calendar.setOption('events', events);
}

function crearModal() {
    // Creamos el modal a continuación del pie
    const footer = document.getElementsByTagName('footer')[0];
    $(footer).append(`
        <div class="modal" id="evento" tabindex="-1">
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

function confirmarFecha(fecha, horaInicio, horaFin, pista, cliente) {
    const botonConfirmar = $('.modal-footer .btn-primary');
    $(botonConfirmar[0]).on('click', function(event) {
  
        let datosAEnviar = JSON.stringify({  
            fecha: fecha,
            horaInicio: horaInicio, 
            pista: pista,
            horaFin, horaFin,
            cliente: cliente,
            informacion: "Reserva realizada por un cliente"
        });

        const formData = new FormData();

        // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

        formData.append("datos", datosAEnviar);

        const p = document.createElement('p');

        p.append(`${datosAEnviar}`);

        p.hidden = true;
        p.id = "datos";

        document.getElementsByTagName('footer')[0].append(p);

        fetch('../servidor/actualizarCalendario.php', {
            method: 'post',
            body: formData
        }).then ((response) => response.text()
        ).then(function (datos) {
            const direccion = "http://localhost/proyecto/public/detallesReserva.php?datos=" + document.getElementById("datos").innerText;
            location.replace(direccion);

        }).catch(function (err) {
            console.log("Ha habido un error");
        });
  
        event.stopPropagation();
    });
}