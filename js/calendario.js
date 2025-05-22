document.addEventListener('DOMContentLoaded', function() {
    calendarioAdministrador();
});

// Función que permite gestionar el calendario para modificar las fechas ocupadas
function calendarioAdministrador(){
    
    var calendario = document.querySelector('#calendario');
    if(calendario != null){
        cargarCalendario(JSON.parse(calendario.outerText));
    }
}

// Función que muestra el calendario
function cargarCalendario(calendario){
    var calendarEl = document.getElementById('calendario');
    // BORRAR CONTENIDO DEL DIV PARA NO MOSTRAR EL CALENDARIO DE LA PISTA, QUE ESTÁ EN EL DIV PARA PODER ACCEDER A ÉL DESDE JAVASCRIPT
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',

        locale: "es",

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,listWeek"
        },

        dateClick:function(info) {
            document.getElementById('evento').style.display = 'block';
        }
    });

    calendar.render();

    var events = new Array();

    for(fecha of calendario) {
        events.push({
            start: fecha.fechaOcupada + "T" + fecha.horaOcupada,
            end: ''
        })
    }

    calendar.setOption('events', events);
}