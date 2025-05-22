<?php
    session_start();
    
    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si se obtiene la variable "datos" (pulsando el botón "Confirmar" del modal de consultarCalendario.php)
    if (isset($_POST['datos'])) {
        $datos = json_decode(($_POST['datos']));

        $pista = $datos['pista'];
        $fecha = explode('T', $datos['fecha']);

        $crud = new Crud(new DB("proyecto"));

    }

    // Si hemos llegado aquí por otros medios (como escribiendo la dirección directamente), redirigimos a la página principal del administrador
    else {
        header("Location: intranet.php");
    }