<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . 'config.php';
    session_start();

    // Si no se obtiene la variable "pista"
    if(!isset($_GET['pista'])){
        header("Location: intranet.php");
        exit(); 
    }

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Si se obtiene la variable "pista" (pinchando en una pista desde reservarPista.php)
    if(isset($_GET['pista'])){
        $crud = new Crud(new DB("proyecto"));
        // Enviamos las fechas ocupadas de esta pista a JavaScript para que actualice el calendario para el cliente
        $calendario = json_encode($crud->listar("*", "reservas", "where pista = $_GET[pista]"));
        echo $calendario;
    }
?>