<?php
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
        <link rel="stylesheet" type="text/css" href="/css/estilosCliente.css">
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
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    // Variables relacionadas con la tabla de reservas
    $filasPorPagina = 10;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $desplazamiento = ($pagina - 1) * $filasPorPagina;

    $crud = new Crud(new DB("proyecto"));
    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
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

    <!-- El contenido principal de la página será la segunda columna -->
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
        <?php
            $reservas = $crud->listar("*", "reservas", "where cliente = \"$_SESSION[cliente]\" ORDER BY fecha, horaInicio ASC LIMIT $filasPorPagina OFFSET $desplazamiento");
            if($reservas == null){
                echo "<h2 class=\"d-flex justify-content-center py-2\">Todavía no ha realizado ninguna reserva</h2>";
                echo "</div>";
            }
            else {
                
                $filasTotales = $crud->listar("count(*)", "reservas", "where cliente = \"$_SESSION[cliente]\"")[0]['count(*)'];
                $totalPaginas = ceil($filasTotales / $filasPorPagina);
                $paginaSiguiente = $pagina + 1;
                $paginaAnterior = $pagina - 1;

                if ($pagina > 1){
                    echo "<a href=\"?pagina=$paginaAnterior\">← Anterior</a>";
                }
                echo "<span>Página $pagina de $totalPaginas></span>";

                if ($pagina < $totalPaginas) {
                    echo "<a href=\"?pagina=$paginaSiguiente\">Siguiente →</a>";
                }

                ?>
                <form method="post" action="../servidor/actualizarCalendario.php">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Hora de inicio</th>
                            <th scope="col">Hora de finalización</th>
                            <th scope="col">Pista</th>
                            <th scope="col">Cancelar reserva</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $horaActual = strtotime("now"); 
                            // Recorremos las reservas y las mostramos
                            foreach($reservas as $reserva){
                                // Guardamos el nombre de la pista para mostrarlo
                                $pista = $crud->obtener("pistas", "where id = $reserva[pista]")[0]['nombre'];
                                echo "<tr>";
                                    echo "<td>$reserva[fecha]</td>";
                                    echo "<td>$reserva[horaInicio]</td>";
                                    echo "<td>$reserva[horaFin]</td>";
                                    echo "<td>$pista</td>";
                                    
                                    $horaReserva = strtotime($reserva['fecha']) + (explode(":", $reserva['horaInicio'])[0] * 60* 60);
                                    // Si todavía no ha pasado la fecha de reserva, se permite cancelarla
                                    if($horaReserva > $horaActual) {
                                        // Guardamos el id de la reserva para poder cancelarla
                                        echo "<td><i class=\"bi bi-x-circle\" data-id=\"$reserva[id]\"></i></td>";
                                    }
                                    else {
                                        echo "<td>Fecha pasada</td>";
                                    }
                                    
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </form>
            </div>
        <?php
            }
        ?>
    </main>
</div>

    <?php
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    ?>