$(document).ready(function() {
    for (const pista of document.querySelectorAll('.accordion-body')) {
        $(pista).on('click', async function(){
            var calendario = new DayPilot.Calendar("calendario", {

            viewType: "Week",
            startDate: "2025-05-12",
            headerDateFormat: "dddd",
            onEventClick: async args => {

                const colors = [
                    {name: "Blue", id: "#3c78d8"},
                    {name: "Green", id: "#6aa84f"},
                    {name: "Yellow", id: "#f1c232"},
                    {name: "Red", id: "#cc0000"},
                ];

                const form = [
                    {name: "Text", id: "text"},
                    {name: "Start", id: "start", type: "datetime"},
                    {name: "End", id: "end", type: "datetime"},
                    {name: "Color", id: "barColor", type: "select", options: colors},
                ];

                const modal = await DayPilot.Modal.form(form, args.e.data);

                if (modal.canceled) {
                    return;
                }

                calendario.events.update(modal.result);

            },
            onBeforeEventRender: args => {
                args.data.barBackColor = "transparent";
                if (!args.data.barColor) {
                    args.data.barColor = "#333";
                }
            },
            onTimeRangeSelected: async args => {

                const form = [
                    {name: "Name", id: "text"}
                ];

                const data = {
                    text: "Event"
                };

                const modal = await DayPilot.Modal.form(form, data);

                calendario.clearSelection();

                if (modal.canceled) {
                    return;
                }

                calendario.events.add({
                    start: args.start,
                    end: args.end,
                    id: DayPilot.guid(),
                    text: modal.result.text,
                    barColor: "#3c78d8"
                });
            },
            onHeaderClick: args => {
                console.log("args", args);
            },
            });

            calendario.startDate = new DayPilot.Date(new Date().toISOString().split('T')[0]);
            calendario.init();
            var events = new Array();

            let url = new URL('http://localhost/proyecto/servidor/obtenerCalendario.php');
            let parametro = {pista: pista.outerText};
            url.search = new URLSearchParams(parametro).toString();

            await fetch(url, {
                method: 'get'
            }).then ((response) => response.json()
            ).then(function (data) {
                
                var contador = 1;
                for(const hora of data){
                    // Rellenamos los horarios ocupados
                    events.push({
                        start: hora['fechaOcupada'] + "T" + hora['horaOcupada'],
                        end: hora['fechaOcupada'] + "T" + hora['horaOcupada'],
                        id: contador,
                        text: "Hora ocupada",
                        barColor: "#CC0000"
                    });

                    contador++;
                }
            }).catch(function (err) {
                console.log("Ha habido un error");
            });

        const app = {
            init() {
                this.loadEvents();
            },
            loadEvents() {
                
                // Añadimos las horas ocupadas al calendario
                calendario.update({events});
            }
        };
        app.init();

        });
    }
});