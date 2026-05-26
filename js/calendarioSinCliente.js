// Hay que importar "esLocale" para arreglar un bug con el idioma
import esLocale from '../node_modules/@fullcalendar/core/locales/es.js';

document.addEventListener('DOMContentLoaded', function() {
    
    // Mostramos el calendario para el cliente cada vez que pinche en una pista para reservarla
    for (const pista of document.querySelectorAll('.accordion-body')) {
        $(pista).on('click', async function(){

            cargarCalendario(pista.outerText, pista.childNodes[1].value);
        });
    }
});

// Función que muestra el calendario
async function cargarCalendario(pista, id){
    var calendarEl = document.getElementById('calendario');
    // Indicamos el título de la pista antes de mostrar el calendario
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
        locale: esLocale,

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