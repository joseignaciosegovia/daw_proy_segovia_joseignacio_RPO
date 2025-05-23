document.addEventListener('DOMContentLoaded', function() {
    // Mostrar calendario para el cliente cada vez que pinche en una pista para reservarla
    for (const pista of document.querySelectorAll('.accordion-body')) {
        $(pista).on('click', async function(){
            cargarCalendario(pista.outerText);
        });
    }
});

// Función que muestra el calendario
function cargarCalendario(pista){

    var calendario = JSON.parse(document.getElementById('calendario').outerText);

    // Sacamos el cliente del array que contenía el calendario y el email del cliente
    const cliente = calendario.pop();

    var calendarEl = document.getElementById('calendario');
    // Borramos el contenido del div para que no muestre la información de la pista y las fechas que ya hemos recogido
    calendarEl.replaceChildren();

    crearModal();

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
            
            // Mostramos el mensaje indicando que se va a añadir un horario ocupado (CORREGIR FORMATO FECHA)
            modalCuerpo.insertAdjacentHTML('afterbegin', `
                Añadir una reserva el ${fecha} a las ${hora}
            `);
            
            const modal = new bootstrap.Modal('#evento');
            modal.show();

            cerrarModal(modal);
            confirmarFecha(fecha, hora, pista, cliente);
        }
    });

    calendar.render();

    var events = new Array();

    // Rellenamos el array de eventos con las fechas ocupadas para la pista
    for(fecha of calendario) {
        if(fecha.pista == pista && fecha.cliente == cliente) {
            events.push({
                start: fecha.fecha + "T" + fecha.hora,
                end: ''
            })
        }
        
    }

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
                    <h5 class="modal-title">Añadir reserva</h5>
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

function confirmarFecha(fecha, hora, pista, cliente) {
    const botonConfirmar = $('.modal-footer .btn-primary');
    $(botonConfirmar[0]).on('click', function(event) {
  
        let datosAEnviar = JSON.stringify({  
            fecha: fecha,
            hora: hora, 
            pista: pista,
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