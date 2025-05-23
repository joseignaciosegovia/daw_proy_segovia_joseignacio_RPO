document.addEventListener('DOMContentLoaded', function() {
    calendarioAdministrador();
});

function quitarFoco() {
    // Select all SVG elements that have both 'aria-hidden="true"' AND 'tabindex="0"'.
    // The attribute selector '[attribute="value"]' is used for precise targeting.
    const problematicSVGs = document.querySelectorAll('svg[aria-hidden="true"][tabindex="0"]');
    // Iterate over each found SVG element.
    problematicSVGs.forEach(svg => {
        // Remove the 'tabindex' attribute from the SVG element.
        // This ensures that the SVG, which is already marked as hidden from accessibility
        // trees, does not unexpectedly receive keyboard focus.
        svg.removeAttribute('tabindex');
        console.log('Removed tabindex="0" from an aria-hidden SVG:', svg);
    });
    // Optional: You might also want to log a message if no problematic SVGs were found.
    if (problematicSVGs.length === 0) {
        console.log('No aria-hidden SVGs with tabindex="0" found. All good!');
    }
}

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
            const modalCuerpo = document.getElementsByClassName('modal-body')[0];

            // Borramos el cuerpo del modal para que no muestre el mensaje anterior
            modalCuerpo.replaceChildren();
            // Mostramos el mensaje indicando que se va a añadir un horario ocupado (CORREGIR FORMATO FECHA)
            modalCuerpo.insertAdjacentHTML('afterbegin', `
                Añadir horario ocupado en la fecha ${info.dateStr}
            `);
            
            const modal = new bootstrap.Modal('#evento');
            modal.show();

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
  
      const modal = new bootstrap.Modal('#evento');
      modal.hide();

      quitarFoco();
  
      //event.stopPropagation();
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
            location.reload();
        }).catch(function (err) {
            console.log("Ha habido un error");
        });
  
        event.stopPropagation();
    });
}