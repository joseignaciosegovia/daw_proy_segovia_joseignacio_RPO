<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    function añadirScriptsPie(){
?>
        <script src="/proyecto/js/cancelarReserva.js"></script>
<?php }

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: perfilCliente.php');
        die();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    $crud = new Crud(new DB("proyecto"));

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Cargamos la cabecera
    require_once "../vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Lista de reservas de $cliente[nombre]</h2>";
?>

    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once "../vista/template/navCliente.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col d-flex align-items-center">
                <?php
                    $reservas = $crud->listar("*", "reservas", "where cliente = \"$_SESSION[cliente]\"");
                    if($reservas != null){
                        ?>
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
                                    foreach($reservas as $reserva){
                                        echo "<tr>";
                                            echo "<td>$reserva[fecha]</td>";
                                            echo "<td>$reserva[horaInicio]</td>";
                                            echo "<td>$reserva[horaFin]</td>";
                                            echo "<td>$reserva[pista]</td>";
                                            echo "<form method=\"post\" action=\"../servidor/actualizarCalendario.php\">";
                                            echo "<input name=\"fecha\" type=\"hidden\" value=\"$reserva[fecha]\">";
                                            echo "<input name=\"horaInicio\" type=\"hidden\" value=\"$reserva[horaInicio]\">";
                                            echo "<input name=\"pista\" type=\"hidden\" value=\"$reserva[pista]\">";
                                            $horaReserva = strtotime($reserva['fecha']) + (explode(":", $reserva['horaInicio'])[0] * 60* 60);
                                            // Si todavía no ha pasado la fecha se permite cancelar la reserva
                                            if($horaReserva > $horaActual) {
                                                echo "<td><i class=\"bi bi-x-circle\"></i></td>";
                                            }
                                            else {
                                                echo "<td>Fecha pasada</td>";
                                            }
                                            echo "</form>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>

    <?php
        // Cargamos el pie
        require_once "../vista/template/footer.php";
    ?>
    </body>
</html>