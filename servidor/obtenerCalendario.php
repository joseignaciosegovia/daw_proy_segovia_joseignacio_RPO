<?php

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    // Si se obtiene la variable "pista" (pinchando en una pista desde reservarPista.php)
    if(isset($_GET['pista'])){
        $crud = new Crud(new DB("proyecto"));
        // Enviamos las fechas ocupadas de esta pista a JavaScript para que actualice el calendario para el cliente
        $calendario = json_encode($crud->listar("*", "reservas", "where pista = \"$_GET[pista]\""));
        echo $calendario;
    }

    // Si hemos llegado a esta página por otros medios (por ejemplo, escribiendo la dirección directamente)
    else {
        // Si hemos iniciado sesión como administrador, redirigimos a la página principal del administrador
        if (!empty($_SESSION["administrador"])) {
            header("Location: intranet.php");
            exit();
        }
        // Si no, vamos al inicio del cliente
        else {
            header("Location: ../public/reservarPista.php");
        }    
    }
?>