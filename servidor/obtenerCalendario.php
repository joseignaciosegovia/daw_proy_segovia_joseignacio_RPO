<?php

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    if(isset($_GET['pista'])){
        $crud = new Crud(new DB("proyecto"));

        $calendario = json_encode($crud->listar("*", "reservas", "where pista = \"$_GET[pista]\""));
        echo $calendario;
    }
?>