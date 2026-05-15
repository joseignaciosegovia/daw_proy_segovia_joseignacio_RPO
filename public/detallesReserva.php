<?php
    ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosCliente.css">
<?php }

    // Devuelve las iniciales de una cadena con distintas palabras
    function iniciales(string $nombre): string {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    // Si hemos recibido los datos de la reserva desde calendarioCliente.js
    if(isset($_GET['datos'])){
        $datosReserva = json_decode(($_GET['datos']));
        $crud = new Crud(new DB("proyecto"));
        $cliente = $crud->obtener("clientes", "where email = \"$datosReserva->cliente\"")[0];
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";

        // Datos que vamos a mostrar
        $fecha = new DateTime();
        // Formato de fecha en español
        $formatter = new IntlDateFormatter(
            'es_ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $iniciales = iniciales($cliente['nombre']);

?>

    <main class="main">
        <div class="welcome-bar">
            <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
            <div class="welcome-text">
                <h1>Bienvenida, <?php echo "$cliente[nombre]"; ?></h1>
                <p>Hoy es <?php echo $formatter->format($fecha);?> &middot; Usuario activo</p>
            </div>
            <span class="badge badge-green">
                <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
            </span>
        </div>
        <div class="card shadow-sm border-0">
            <h2>Detalles de la reserva</h2>
            <div class="accordion accordion-flush">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Pista</th>
                            <th>Fecha</th>
                            <th>Hora de inicio</th>
                            <th>Hora de fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $datosReserva->pista ?></td>
                            <td><?php echo $datosReserva->fecha ?></td>
                            <td><?php echo $datosReserva->horaInicio ?></td>
                            <td><?php echo $datosReserva->horaFin ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    }

    // Si hemos accedido a esta página de otra forma (por ejemplo, escribiendo la dirección), redirigimos a la página de inicio del cliente
    else {
        header("Location: inicioCliente.php");
    }
?>