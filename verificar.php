<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['codigo']) && !empty($_GET['codigo'])){
        $crud = new Crud(new DB("proyecto"));
	    $devolver = $crud->listar("email, codigo", "clientes", "where email = \"$_GET[email]\" and codigo \"$_GET[codigo]\" and activo = 0");
        // Si tenemos una coincidencia
        if(count($devolver) > 0){
            $crud->actualizar("clientes", "active = 1", "where emai email = \"$_GET[email]\" and codigo \"$_GET[codigo]\" and activo = 0");
        // Si no hay una coincidencia
        }else{
            echo '<div class="statusmsg">La URL no es válida o la cuenta ya está activa.</div>';            
        }
    }else{
        echo '<div class="statusmsg">Por favor, usa el enlace que se le ha enviado al email.</div>';
    }


    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";