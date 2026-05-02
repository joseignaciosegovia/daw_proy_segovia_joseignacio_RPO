<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/cancelarReserva.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));
    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Lista de reservas de $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
?>

    <!-- El contenido principal de la página será la segunda columna -->
    <div class="col-12 col-sm-6 col-md-7 col-lg-8">
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
                            // Recorremos las reservas y las mostramos
                            foreach($reservas as $reserva){
                                // Guardamos el nombre de la pista para mostrarlo
                                $pista = $crud->obtener("pistas", "where id = $reserva[pista]")[0]['nombre'];
                                echo "<tr>";
                                    echo "<td>$reserva[fecha]</td>";
                                    echo "<td>$reserva[horaInicio]</td>";
                                    echo "<td>$reserva[horaFin]</td>";
                                    echo "<td>$pista</td>";
                                    echo "<form method=\"post\" action=\"../servidor/actualizarCalendario.php\">";
                                    $horaReserva = strtotime($reserva['fecha']) + (explode(":", $reserva['horaInicio'])[0] * 60* 60);
                                    // Si todavía no ha pasado la fecha de reserva, se permite cancelarla
                                    if($horaReserva > $horaActual) {
                                        // Guardamos el id de la reserva para poder cancelarla
                                        echo "<td><i class=\"bi bi-x-circle\" data-id=\"$reserva[id]\"></i></td>";
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

    <?php
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    ?>
    </body>
</html>