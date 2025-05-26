<?php

    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/modelo/Cliente.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script src="/proyecto/js/calendarioCliente.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página principal
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Si hemos recibido los datos de la reserva
    if(isset($_GET['datos'])){
        require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/nav.php";

        $datos = $datos = json_decode(($_GET['datos']));
?>

    <div class="col">
        <h4>Detalles de la reserva</h4>
        <div class="accordion accordion-flush">
            <table class="table table-hover">
                <thead>
                    <th>Pista</th>
                    <th>Precio</th>
                    <th>Fecha</th>
                    <th>Hora de inicio</th>
                    <th>Hora de fin</th>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $datos->pista ?></td>
                        <td><?php echo $datos->pista ?></td>
                        <td><?php echo $datos->fecha ?></td>
                        <td><?php echo $datos->horaInicio ?></td>
                        <td><?php echo $datos->horaFin ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

<?php 
    }

    // Si hemos accedido a esta página de otra forma (como introduciendo la dirección), nos vamos a la página de inicio del usuario
    else {
        header("Location: reservarPista.php");
    }

    require_once "../vista/template/footer.php";
?>