<?php

    session_start();

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Si hemos recibido los datos de la reserva desde calendarioCliente.js
    if(isset($_GET['datos'])){
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";

        $datosReserva = json_decode(($_GET['datos']));
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
                        <td><?php echo $datosReserva->pista ?></td>
                        <td><?php echo $datosReserva->pista ?></td>
                        <td><?php echo $datosReserva->fecha ?></td>
                        <td><?php echo $datosReserva->horaInicio ?></td>
                        <td><?php echo $datosReserva->horaFin ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    }

    // Si hemos accedido a esta página de otra forma (por ejemplo, escribiendo la dirección), redirigimos a la página de inicio del cliente
    else {
        header("Location: reservarPista.php");
    }
?>