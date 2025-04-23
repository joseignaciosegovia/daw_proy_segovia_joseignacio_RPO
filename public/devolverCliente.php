<?php
    session_start();

    require_once "../controlador/Crud.php";

    $nombreCliente = $_SESSION['cliente'];

    if($nombreCliente) {
        $crud = new Crud();
        $cliente = $crud->obtenerDatos("clientes", ["usuario" => $nombreCliente], []);

        $clienteJSON = json_encode($cliente);
        echo $clienteJSON;
    }

    else {
        $noCliente = json_encode(new stdClass);
        echo $noCliente;
    }

?>