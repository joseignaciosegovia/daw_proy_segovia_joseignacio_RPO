document.addEventListener('DOMContentLoaded', function() {
    //calendarioAdministrador();
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

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',

        locale: "es",

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,listWeek"
        },

        // Al pinchar en el calendario, mostraremos un modal para crear un evento
        dateClick: function(info) {
            const modalCuerpo = document.getElementsByClassName('modal-body')[0];

            const fechaHora = info.dateStr;
            const indiceInicio = fechaHora.indexOf('T');
            const indiceFin= fechaHora.indexOf('+');
            const fecha = fechaHora.substring(0, indiceInicio);
            const hora = fechaHora.substring(indiceInicio + 1, indiceFin);

            // Borramos el cuerpo del modal para que no muestre el mensaje anterior
            modalCuerpo.replaceChildren();

            const titulo = document.getElementsByClassName('modal-title')[0];
            titulo.innerHTML = "Horario reservado el " + fecha + " a las " + hora;
            // Mostramos el mensaje indicando que se va a añadir un horario ocupado (CORREGIR FORMATO FECHA)
            modalCuerpo.insertAdjacentHTML('afterbegin', `
                
                <label for="informacion">Indique la información sobre el horario (quién lo ha ocupado)</label>
                <textarea id="informacion" rows="5" cols="50"></textarea>
            `);
            
            const modal = new bootstrap.Modal('#evento');
            modal.show();

            cerrarModal(modal);
            confirmarFecha(fecha, hora, pista);
        }
    });

    calendar.render();

    var events = new Array();

    // Rellenamos el array de eventos con las fechas ocupadas para la pista
    for(fecha of calendario) {
        events.push({
            title: fecha.informacion,
            start: fecha.fecha + "T" + fecha.hora,
            end: ''
        })
    }

    // Indicamos los eventos para el calendario
    calendar.setOption('events', events);
}

function cerrarModal(modal) {
    const botonCerrar = $('.modal-footer .btn-secondary');
    $(botonCerrar[0]).on('click', function(event) {
  
        // Ocultamos el modal
        modal.hide();
  
        event.stopPropagation();
    });
}

function confirmarFecha(fecha, hora, pista) {
    const botonConfirmar = $('.modal-footer .btn-primary');
    $(botonConfirmar[0]).on('click', function(event) {

        const informacion = document.getElementById("informacion").value;
  
        let datosAEnviar = JSON.stringify({  
            fecha: fecha,
            hora: hora, 
            pista: pista,
            informacion: informacion
        });

        const formData = new FormData();

        // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

        formData.append("datos", datosAEnviar);

        fetch('actualizarCalendario.php', {
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