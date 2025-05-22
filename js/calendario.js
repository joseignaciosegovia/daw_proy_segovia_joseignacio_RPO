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

    // Sacamos la pista del array que contenía el calendario y el nombre de la pista
    const pista = calendario.pop();

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

        // Al pinchar en el calendario, mostraremos un modal para crear un evento (NO FUNCIONA)
        dateClick:function(info) {
            const modal = document.getElementById('evento');
            modal.style.display = 'block';

            

            $(modal.getElementsByClassName('modal-body')).append(`
                Añadir horario ocupado en la fecha ${info.dateStr}
            `);
            
            const modalBootstrap = new bootstrap.Modal('#evento');
            modalBootstrap.show();

            cerrarModal();
            confirmarFecha(info.dateStr, pista);
        }
    });

    calendar.render();

    var events = new Array();

    // Rellenamos el array de eventos con las fechas ocupadas para la pista
    for(fecha of calendario) {
        events.push({
            start: fecha.fechaOcupada + "T" + fecha.horaOcupada,
            end: ''
        })
    }

    // Indicamos los eventos para el calendario
    calendar.setOption('events', events);
}

function cerrarModal() {
    const botonCerrar = $('.modal-footer .btn-secondary');
    $(botonCerrar[0]).on('click', function(event) {
  
      // Obtenemos el modal y lo ocultamos
  
      const modalBootstrap = new bootstrap.Modal('#evento');
      modalBootstrap.hide();
  
      event.stopPropagation();
    });
}

function confirmarFecha(fecha, pista) {
    const botonConfirmar = $('.modal-footer .btn-primary');
    $(botonConfirmar[0]).on('click', function(event) {
  
        // LOS DATOS DEBEN TENER EL FORMATO ADECUADO
        let datosAEnviar = JSON.stringify({ 
            pista: pista, 
            fecha: fecha
        });

        const formData = new FormData();

        // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

        formData.append("datos", datosAEnviar);

        fetch('actualizarCalendario.php', {
            method: 'post',
            body: formData
        }).then ((response) => response.json()
        ).then(function (data) {

        }).catch(function (err) {
            console.log("Ha habido un error");
        });
  
        event.stopPropagation();
    });
}