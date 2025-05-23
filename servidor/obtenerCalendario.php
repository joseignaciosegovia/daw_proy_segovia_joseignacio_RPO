<?php

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    if(isset($_GET['pista'])){
        $crud = new Crud(new DB("proyecto"));

        // CONTEMPLAR LA OPCIÓN DE QUE NO HAYA NINGUNA FECHA OCUPADA PARA ESA PISTA

        $calendario = json_encode($crud->listar("*", "reservas", "where pista = \"$_GET[pista]\""));
        echo $calendario;
    }
?>