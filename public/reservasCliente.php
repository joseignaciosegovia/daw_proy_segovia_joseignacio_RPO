<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . 'config.php';
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

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/cancelarReserva.js"></script>
<?php }

    // Devuelve las iniciales de una cadena con distintas palabras
    function iniciales(string $nombre): string {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                // Para cada palabra, nos quedamos con la primera letra y la transformamos a mayúscula
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    // Variables relacionadas con la tabla de reservas
    $filasPorPagina = 10;
    // Por defecto, estaremos en la página 1
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $desplazamiento = ($pagina - 1) * $filasPorPagina;

    $crud = new Crud(new DB("proyecto"));
    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    // Guardamos el cliente para que puedan mostrarse sus datos en la barra de navegación
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
    // Datos que vamos a mostrar
    $fecha = new DateTime();
    // Formato de fecha en español
    $formatoFecha = new IntlDateFormatter(
        // fecha en español
        'es_ES',
        // Formato Martes, 12 de abril de 1952 d. C. o 15:30:42 h (hora del Pacífico)
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE
    );
    // Guardamos las iniciales del nombre completo del usuario
    $iniciales = iniciales($cliente['nombre']);
?>
    <main class="main">
        <!-- BIENVENIDA -->
        <div class="welcome-bar">
            <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
            <div class="welcome-text">
                <h1>Bienvenida/o, <?php echo "$cliente[nombre]"; ?></h1>
                <p>Hoy es <?php echo $formatoFecha->format($fecha);?></p>
            </div>
            <span class="badge badge-green">
                <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
            </span>
        </div>
        <div class="card shadow-sm border-0">
            <div class="p-3 py-4">
                <div class="seccionSubtitulo mb-4">
        <?php
            // Obtenemos tantas reservas como filas por página, empezando por la que toque para la página en la que nos encontremos
            $reservas = $crud->listar("*", "reservas", "where cliente = \"$_SESSION[cliente]\" ORDER BY fecha, horaInicio, pista ASC LIMIT $filasPorPagina OFFSET $desplazamiento");
            // Si el usuario no ha hecho ninguna reserva
            if($reservas == null){
?>
                    <i class="ti ti-circle-number-0" aria-hidden="true"></i>
                    <div>
                        <h2>No has realizado ninguna reserva</h2>
                        <small class="text-muted">Cuando realices reservas podrás consultarlas aquí</small>
                    </div>
                </div>
<?php
            }
            // Si el usuario ha hecho reservas
            else {
?>
                    <i class="ti ti-calendar" aria-hidden="true"></i>
                    <div>
                        <h2>Historial de reservas</h2>
                        <small class="text-muted">Reservas realizadas anteriormente</small>
                    </div>
                </div>
<?php
                $filasTotales = $crud->listar("count(*)", "reservas", "where cliente = \"$_SESSION[cliente]\"")[0]['count(*)'];
                // Redondeamos al número superior para saber cuántas páginas tendrá la tabla
                $totalPaginas = ceil($filasTotales / $filasPorPagina);
                $paginaSiguiente = $pagina + 1;
                $paginaAnterior = $pagina - 1;
                // Si nos encontramos en una página que no sea la primera, mostramos una opción para volver a la página anterior
                if ($pagina > 1){
                    echo "<a href=\"?pagina=$paginaAnterior\">← Anterior</a> ";
                }
                // Mostramos la página en la que nos encontramos
                echo "<span>Página $pagina de $totalPaginas></span>";
                // Si no estamos en la última página, mostramos una opción para ir a la siguiente
                if ($pagina < $totalPaginas) {
                    echo " <a href=\"?pagina=$paginaSiguiente\">Siguiente →</a>";
                }
?>
                <div class="table-responsive">
                    <!-- text-nowrap para que el texto de cada fila no ocupe más de una línea -->
                    <table class="table table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Hora de inicio</th>
                                <th scope="col">Hora de finalización</th>
                                <th scope="col">Pista</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Cancelar reserva</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Recorremos las reservas y las mostramos
                                foreach($reservas as $reserva){
                                    // Guardamos el nombre de la pista para mostrarlo
                                    $pista = $crud->obtener("pistas", "where id = $reserva[pista]")[0]['nombre'];
                                    $precio = $crud->listar("precioReserva", "pistas", "where id = $reserva[pista]")[0]['precioReserva'];
                                    echo "<tr>";
                                        echo "<td>$reserva[fecha]</td>";
                                        echo "<td>$reserva[horaInicio]</td>";
                                        echo "<td>$reserva[horaFin]</td>";
                                        echo "<td>$pista</td>";
                                        echo "<td>$precio €</td>";
                                        
                                        $fechaReserva = $reserva['fecha'] . " " . $reserva['horaInicio'];
                                        // Fecha actual con la hora de Madrid
                                        $fechaMadrid = new DateTime('now', new DateTimeZone('Europe/Madrid'));
                                        // Fecha actual con un formato que permita comparar con la fecha de la reserva
                                        $fechaActual = $fechaMadrid->format('Y-m-d H:i:s');
                                        // Si todavía no se ha pasado la fecha de reserva, se permite cancelarla
                                        if($fechaReserva > $fechaActual) {
                                            // Guardamos el id de la reserva en un atributo personalizado para poder cancelarla en cancelaReserva.js
                                            echo "<td><button class=\"cancelarReserva btn btn-danger\" data-id=\"$reserva[id]\">Cancelar</button></td>";
                                        }
                                        else {
                                            echo "<td>Fecha pasada</td>";
                                        }
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
        <?php
            }
        ?>
            </div>
        </div>
    </main>
    <!-- Cerramos la sección principal, creada en navCliente.php -->
</div>
    <?php
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    ?>