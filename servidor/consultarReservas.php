<?php
    // ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como gestor o no obtenemos la variable "pista", volvemos a la página de inicio de la intranet
    if (empty($_SESSION["gestor"]) || !isset($_GET['pista'])) {
        header("Location: intranet.php");
        exit();
    }

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/editarReserva.js"></script>
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

    // Función que muestra un mensaje de error (en caso de que haya habido algún problema) y redirige a la página principal
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: intranet.php');
        die();
    }

    // Variables relacionadas con la tabla de reservas
    $filasPorPagina = 10;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $desplazamiento = ($pagina - 1) * $filasPorPagina;
    
    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas · Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de intranet.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));
        // Guardamos el gestor para que puedan mostrarse sus datos en la barra de navegación
        $gestor = $crud->obtener("gestores", "where email = \"$_SESSION[gestor]\"")[0];
        $fecha = new DateTime();
        // Formato de fecha en español
        $formatter = new IntlDateFormatter(
            'es_ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $iniciales = iniciales($gestor['nombre']);

        $pista = $crud->obtener("pistas", "where id = $_GET[pista]")[0]['nombre'];
        $nombre = $crud->listar("nombre", "gestores", "where email = \"$_SESSION[gestor]\"")[0]['nombre'];

        $reservas = $crud->listar("*", "reservas", "where pista = $_GET[pista] ORDER BY fecha, horaInicio ASC LIMIT $filasPorPagina OFFSET $desplazamiento");
?>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
        
        <main class="main">
            <!-- BIENVENIDA -->
            <div class="welcome-bar">
                <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
                <div class="welcome-text">
                    <h1>Bienvenida/o, <?php echo "$gestor[nombre]"; ?></h1>
                    <p>Hoy es <?php echo $formatter->format($fecha);?></p>
                </div>
                <span class="badge badge-green">
                    <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
                </span>
            </div>
            <div class="card shadow-sm border-0">
                <div class="p-3 py-4">
            <?php
                // Si no hay reservas en esta pista
                if($reservas == null){
            ?>
                    <div class="section-header mb-4">
                        <i class="ti ti-circle-number-0"></i>
                        <div>
                            <h2>No hay reservas en la pista <?php echo "$pista" ?></h2>
                            <small class="text-muted"><small class="text-muted">Cuando haya reservas en la pista <?php echo "$pista" ?> podrás consultarlas aquí</small>
                        </div>
                    </div>
            <?php }
                // Si hay reservas
                else {
            ?>
                    <div class="section-header mb-4">
                        <i class="ti ti-calendar"></i>
                        <div>
                            <h2>Reservas de la pista <?php echo "$pista" ?></h2>
                            <small class="text-muted">Consulta los horarios reservados de la pista <?php echo "$pista" ?></small>
                        </div>
                    </div>
            <?php
                    $filasTotales = $crud->listar("count(*)", "reservas", "where pista = $_GET[pista]")[0]['count(*)'];
                    $totalPaginas = ceil($filasTotales / $filasPorPagina);
                    $paginaSiguiente = $pagina + 1;
                    $paginaAnterior = $pagina - 1;

                    if ($pagina > 1){
                        echo "<a href=\"?pista=$_GET[pista]&pagina=$paginaAnterior\">← Anterior</a>";
                    }
                    echo "<span> Página $pagina de $totalPaginas </span>";

                    if ($pagina < $totalPaginas) {
                        echo "<a href=\"?pista=$_GET[pista]&pagina=$paginaSiguiente\">Siguiente →</a>";
                    }
            ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Hora de inicio</th>
                                    <th>Hora de Fin</th>
                                    <th>Cliente</th>
                                    <th>Información</th>
                                    <th>Editar</th>
                                    <th hidden></th>
                                    <th hidden></th>
                                </tr>
                            </thead>
                            <tbody>
                    <?php
                        $cont = 1;
                        
                        // Recorremos las reservas y las añadimos a la tabla
                        foreach($reservas as $reserva){
                            if($reserva['cliente'] == null) {
                                $cliente = "-";
                            }
                            else {
                                $cliente = $reserva['cliente'];
                            }
                    ?>
                            <tr>
                                <!-- Guardamos el id de la pista para poder actualizar la reserva desde actualizarCalendario.php -->
                                <td hidden><?php echo $_GET['pista'] ?></td>
                                <!-- Guardamos el id de la reserva para poder actualizarla desde actualizarCalendario.php -->
                                <td hidden><?php echo $reserva['id'] ?></td>
                                <td><?php echo $cont ?></td>
                                <td><?php echo $reserva['fecha'] ?></td>
                                <td><?php echo $reserva['horaInicio'] ?></td>
                                <td><?php echo $reserva['horaFin'] ?></td>
                                <td><?php echo $cliente ?></td>
                                <td><?php echo $reserva['informacion'] ?></td>
                            <?php
                                $fechaReserva = $reserva['fecha'] . " " . $reserva['horaInicio'];
                                $zonaHoraria = new DateTimeZone('Europe/Madrid');
                                $fechaMadrid = new DateTime('now', $zonaHoraria);
                                $fechaActual = $fechaMadrid->format('Y-m-d H:i:s');
                                // Si la reserva no se ha pasado, el gestor podrá modificarla
                                if($fechaReserva > $fechaActual) {
                                    echo "<td><button class=\"editarPista btn btn-warning\">Editar</button></td>";
                                }
                                else {
                                    echo "<td>Fecha pasada</td>";
                                }
                            ?>
                        </tr>
                    <?php 
                            $cont++;
                        }
                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="mt-2 text-start">
            <button class="btn btn-secondary" onclick="window.location.href='intranet.php';">Volver atrás</button>
        </div>
    </main>
</div>
<?php
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>