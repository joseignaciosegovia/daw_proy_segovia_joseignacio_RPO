<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Si hemos recibido el correo y el código del usuario
    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['codigo']) && !empty($_GET['codigo'])){
        $crud = new Crud(new DB("proyecto"));
	    $devolver = $crud->listar("email, codigo", "clientes", "where email = \"$_GET[email]\" and codigo = \"$_GET[codigo]\" and activo = 0");
        // Si tenemos una coincidencia, significa que hay que validar el usuario
        if(count($devolver) > 0){
            $crud->actualizar("clientes", "activo = 1", "where email = \"$_GET[email]\" and codigo = \"$_GET[codigo]\" and activo = 0");
            echo '<div>Ha activado el usuario con email ' . $_GET['email'] . '</div>';
        } 
        // Si no hay una coincidencia
        else {
            echo '<div>La URL no es válida o la cuenta ya está activa.</div>';       
        }
    } 
    // Si no se ha recibido el correo y el código del usuario, es porque no hemos accedido de la manera adecuada a esta página
    else {
        echo '<div>Por favor, usa el enlace que se le ha enviado al email.</div>';
    }

    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";