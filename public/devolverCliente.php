<?php
    session_start();

    use Clases\DB;
    require_once "../controlador/Crud.php";

    $nombreCliente = $_SESSION['cliente'];

    if($nombreCliente) {
        $crud = new Crud(new DB("proyecto"));
        $cliente = $crud->obtener("clientes", "where usuario = $nombreCliente");

        $clienteJSON = json_encode($cliente);
        echo $clienteJSON;
    }

    else {
        $noCliente = json_encode(new stdClass);
        echo $noCliente;
    }

?>