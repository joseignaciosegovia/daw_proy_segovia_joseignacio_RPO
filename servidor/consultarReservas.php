<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    function añadirScriptsPie(){
?>
        <script type="module" src="/proyecto/js/editarReserva.js"></script>
<?php }

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: intranet.php');
        die();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de accesoAdministrador.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        $reservas = $crud->listar("*", "reservas", "where pista = \"$_GET[pista]\"");
        if($reservas == null){
            // NO IMPRIME EL MENSAJE, SINO QUE DIRECTAMENTE VA A intranet.php
            error("No hay ninguna reserva para la pista " . $_GET['pista']);
        }
?>

        <h1>Reservas de la pista <?php echo "$_GET[pista]" ?></h1>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once "../vista/template/navGestor.php"; ?>

                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col-12 col-lg-8 d-flex align-items-center">
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
                        
                        foreach($reservas as $reserva){
                            if($reserva['cliente'] == null) {
                                $cliente = "-";
                            }
                            else {
                                $cliente = $reserva['cliente'];
                            }
                    ?>
                        <tr>
                            <td hidden><?php echo $_GET['pista'] ?></td>
                            <td><?php echo $cont ?></td>
                            <td><?php echo $reserva['fecha'] ?></td>
                            <td><?php echo $reserva['horaInicio'] ?></td>
                            <td><?php echo $reserva['horaFin'] ?></td>
                            <td><?php echo $cliente ?></td>
                            <td><?php echo $reserva['informacion'] ?></td>
                            <?php
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
        </div>
        <a href="intranet.php"><button>Volver atrás</button></a>
    </body>
</html>

<?php
        if (isset($_SESSION['error'])) {
            echo "<div class='mt-3 text-danger font-weight-bold text-lg'>";
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            echo "</div>";
        }
    }

    else {
        header('Location: intranet.php');
        die();
    }

    require_once "../vista/template/footer.php";
?>