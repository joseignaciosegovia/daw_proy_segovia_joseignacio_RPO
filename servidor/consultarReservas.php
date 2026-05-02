<?php
    ob_start(); // activa el buffer
    session_start();

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/editarReserva.js"></script>
<?php }

    // Función que muestra un mensaje de error (en caso de que haya habido algún problema) y redirige a la página principal
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: intranet.php');
        die();
    }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de intranet.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        $pista = $crud->obtener("pistas", "where nombre = \"$_GET[pista]\"")[0]['id'];
        $nombre = $crud->listar("nombre", "gestores", "where email = \"$_SESSION[gestor]\"")[0]['nombre'];

        $reservas = $crud->listar("*", "reservas", "where pista = $pista");
?>
        <h1 class="d-flex justify-content-center">Bienvenido/a <?php echo $nombre ?></h1>
        <h1 class="d-flex justify-content-center">Reservas de la pista <?php echo "$_GET[pista]" ?></h1>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col-12 col-lg-8 d-flex align-items-center">
            <?php
                // Si no hay reservas en esta pista
                if($reservas == null){
                    echo "<h2 class=\"d-flex justify-content-center\">No hay reservas en esta pista</h2>";
                }
                // Si hay reservas, creamos una tabla y las mostramos
                else {
            ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Hora de inicio</th>
                            <th>Hora de Fin</th>
                            <th>Cliente</th>
                            <th>Información</th>
                            <th>Editar</th>
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
                            <td hidden><?php echo $pista ?></td>
                            <!-- Guardamos el id de la reserva para poder actualizarla desde actualizarCalendario.php -->
                            <td hidden><?php echo $reserva['id'] ?></td>
                            <td><?php echo $cont ?></td>
                            <td><?php echo $reserva['fecha'] ?></td>
                            <td><?php echo $reserva['horaInicio'] ?></td>
                            <td><?php echo $reserva['horaFin'] ?></td>
                            <td><?php echo $cliente ?></td>
                            <td><?php echo $reserva['informacion'] ?></td>
                            <?php
                                // Si una reserva ha sido añadida a mano por el gestor, desde aquí podrá modificarla
                                if($cliente == "-") {
                                    echo "<td><button class=\"editarPista\">Editar</button></td>";
                                }
                                else {
                                    echo "<td>-</td>";
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
            <?php } ?>
        </div>
        <a href="intranet.php"><button>Volver atrás</button></a>
    </body>
</html>

<?php
    }

    else {
        header('Location: intranet.php');
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>