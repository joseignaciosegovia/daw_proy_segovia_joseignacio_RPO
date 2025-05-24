<?php
    session_start();
    
    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    // Si el administrador ha añadido una fecha ocupada o un cliente ha realizado una reserva)
    if (isset($_POST['datos'])) {
        $datos = json_decode(($_POST['datos']));

        $crud = new Crud(new DB("proyecto"));
        if($datos->cliente == null) {
            $crud->insertar("reservas", "\"$datos->fecha\", \"$datos->hora\", \"$datos->pista\", null, \"$datos->informacion\"");
        }
        else {
            $crud->insertar("reservas", "\"$datos->fecha\", \"$datos->hora\", \"$datos->pista\", \"$datos->cliente\", \"$datos->informacion\"");
        }
    }

    // Si el cliente cancela una reserva
    if(isset($_POST['cancelar'])) {
        $reserva = json_decode($_POST['cancelar']);
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("reservas", "where fecha = \"$reserva->fecha\" and hora = \"$reserva->hora\" and pista = \"$reserva->pista\"");
        header("Location: ../public/reservasCliente.php");
    }

    // Si hemos llegado aquí por otros medios (como escribiendo la dirección directamente)
    else {
        // Si hemos iniciado sesión como administrador, redirigimos a la página principal del administrador
        if (!empty($_SESSION["administrador"])) {
            header("Location: intranet.php");
            exit();
        }
        // Si no, vamos al inicio
        else {
            header("Location: ../public/reservarPista.php");
        }    
    }