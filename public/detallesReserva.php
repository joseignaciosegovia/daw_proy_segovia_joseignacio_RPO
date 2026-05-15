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

    // Si hemos recibido los datos de la reserva desde calendarioCliente.js
    if(isset($_GET['datos'])){
        $datosReserva = json_decode(($_GET['datos']));
        $crud = new Crud(new DB("proyecto"));
        $cliente = $crud->obtener("clientes", "where email = \"$datosReserva->cliente\"")[0];
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";

        
?>

    <div class="col">
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
</div>

<?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    }

    // Si hemos accedido a esta página de otra forma (por ejemplo, escribiendo la dirección), redirigimos a la página de inicio del cliente
    else {
        header("Location: inicioCliente.php");
    }
?>