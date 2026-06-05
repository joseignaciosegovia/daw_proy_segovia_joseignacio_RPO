document.addEventListener('DOMContentLoaded', function() {
    // Mostramos el calendario para el cliente cada vez que pinche en una pista para reservarla
    for (const pista of document.querySelectorAll('.accordion-body')) {
        $(pista).on('click', async function(){
            // Guardamos la sección del acordeón que incluye el título de la pista y su precio
            var acordeon = $(pista)[0];
            cargarCalendario(acordeon, pista.childNodes[1].value);
        });
    }
});

// Función que muestra el calendario
async function cargarCalendario(pista, id){
    crearModal();
    // Obtenemos el email del cliente
    const cliente = document.getElementById('cliente').outerText;

    var precio = pista.getElementsByTagName("span")[1].outerText;
    var pista = pista.getElementsByTagName("span")[0].outerText;

    var calendarEl = document.getElementById('calendario');
    // Indicamos la pista antes de mostrar el calendario
    var tituloPista = document.getElementById('tituloPista');
    // Borramos el contenido del div para que no muestre el título de la pista que mostramos anteriormente
    tituloPista.replaceChildren();
    // Añadimos el título de la pista seleccionada y la leyenda del calendario
    $(tituloPista).append(`
        <h3>Pista: ${pista}</h3>
        <div class="d-flex justify-content-center" id="leyenda">
            <span><span class="color" style="background:#4EB272;"></span>Disponible</span>
            <span><span class="color" style="background:#F87171;"></span>Ocupado</span>
        </div>
    `);
    calendarEl.before(tituloPista);
    // Borramos el contenido del div para que no muestre la información de la pista y las fechas que ya hemos recogido
    calendarEl.replaceChildren();

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',

        firstDay: 1,
        // Ponemos el calendario en español
        locale: 'es',

        // Solo se muestran horas enteras
        slotDuration: "01:00:00",

        // No mostrar sábados y domingos
        hiddenDays: [ 6, 0 ],

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
            right: "dayGridMonth,timeGridWeek,timeGridDay"
        },

        dayMaxEvents: true, 

        // Al pinchar en el calendario, mostraremos un modal para crear un evento
        dateClick: function(info) {
            const fechaCompleta = info.date;
            
            const fecha = info.dateStr.substring(0, info.dateStr.indexOf('T'));
            const horaInicio = info.dateStr.substring(info.dateStr.indexOf('T') +1, info.dateStr.indexOf('+'));
            var horaFin = (fechaCompleta.getHours() +1) + ":00:00";

            if (horaFin.indexOf(":") == 1)
                horaFin = "0" + horaFin;

            const horaActual = Date.parse(new Date()) / 1000 / 60 / 60;
            const horaReserva = fechaCompleta.getTime() / 1000 / 60 / 60;
            
            const modalBotonConfirmar = document.getElementsByClassName('modal-footer')[0].getElementsByClassName('btn-success')[0];
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
                    Añadir una reserva el ${fecha} a las ${horaInicio}<br>
                    ${precio} €<br>
                    ¡Advertencia: Si cancela una reserva menos de doce horas antes de la fecha reservada, no se le devolverá el dinero!
                `);

                confirmarFecha(fecha, horaInicio, horaFin, pista, id, cliente);
            }
            
            const modal = new bootstrap.Modal('#modal');
            modal.show();

            cerrarModal(modal);
        }
    });

    calendar.render();

    var events = new Array();

    // URL del servidor
    const BASE_URL = window.location.origin;

    let url = new URL(BASE_URL + '/servidor/obtenerCalendario.php');
    let parametro = {pista: id};
    url.search = new URLSearchParams(parametro).toString();

    // Obtenemos las fechas ocupadas de esta pista

    await fetch(url, {
        method: 'get'
    }).then ((response) => response.json()
    ).then(function (reservas) {
        // Si hay fechas ocupadas para esta pista, las añadimos como eventos del calendario
        if(reservas != null) {
            for(const reserva of reservas){
                // Rellenamos los horarios ocupados
                events.push({
                    start: reserva.fecha + "T" + reserva.horaInicio,
                    end: reserva.fecha + "T" + reserva.horaFin,
                    backgroundColor: "#F87171",
                    borderColor: "#F87171"
                });
            }
        }
        
    }).catch(function (err) {
        console.log("Ha habido un error");
    });

    // Añadimos los eventos en el calendario
    calendar.setOption('events', events);
}

function confirmarFecha(fecha, horaInicio, horaFin, pista, id, cliente) {
    const botonConfirmar = $('.modal-footer .btn-success');
    $(botonConfirmar[0]).off('click').on('click', function(event) {
        let datosAEnviar = JSON.stringify({  
            fecha: fecha,
            horaInicio: horaInicio, 
            pista: pista,
            id: id,
            horaFin, horaFin,
            cliente: cliente,
            informacion: "Reserva realizada por un cliente"
        });

        const formData = new FormData();

        // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

        formData.append("datos", datosAEnviar);

        // Creamos un párrafo en el que guardamos los datos para poder enviarlos a detallesReserva.php
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
            // Después de actualizar el calendario, accedemos a detallesReserva para que el cliente vea los detalles de la reserva
            const direccion = "/public/detallesReserva.php?datos=" + document.getElementById("datos").innerText;
            location.replace(direccion);

        }).catch(function (err) {
            console.log("Ha habido un error");
        });
  
        event.stopPropagation();
    });
}

// Función que crea un modal
function crearModal() {
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
                        <button type="button" class="btn btn-success">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>  
    `);
}

// Función que oculta el modal recibido al pulsar en el botón de cerrar
function cerrarModal(modal) {
    const botonCerrar = $('.modal-footer .btn-secondary');
    $(botonCerrar[0]).off('click').on('click', function(event) {
        // Ocultamos el modal
        modal.hide();
  
        event.stopPropagation();
    });
}