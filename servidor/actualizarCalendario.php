<?php
    session_start();
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si el administrador ha añadido una fecha ocupada o un cliente ha realizado una reserva
    if (isset($_POST['datos'])) {
        $datos = json_decode(($_POST['datos']));

        $crud = new Crud(new DB("proyecto"));
        // Si la reserva no tiene cliente
        if($datos->cliente == null) {
            $crud->insertarColumnas("reservas", "(fecha, horaInicio, horaFin, pista, cliente, informacion)", "\"$datos->fecha\", \"$datos->horaInicio\", \"$datos->horaFin\", \"$datos->id\", null, \"$datos->informacion\"");
        }
        else {
            $crud->insertarColumnas("reservas", "(fecha, horaInicio, horaFin, pista, cliente, informacion)", "\"$datos->fecha\", \"$datos->horaInicio\", \"$datos->horaFin\", \"$datos->id\", \"$datos->cliente\", \"$datos->informacion\"");
        }
    }

    // Si el administrador edita una reserva
    if(isset($_POST['Confirmar'])) {
        $editar = json_decode(($_POST['Confirmar']));
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("reservas", "fecha = \"$editar->fecha\", horaInicio = \"$editar->horaInicio\", horaFin = \"$editar->horaFin\", informacion = \"$editar->informacion\"", "where id = $editar->id");
    }

    // Si el administrador borra una reserva
    if(isset($_POST['Borrar'])) {
        $borrar = json_decode(($_POST['Borrar']));
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("reservas", "where id = $borrar->id");
    }

    // Si el cliente cancela una reserva
    if(isset($_POST['cancelar'])) {
        $reserva = json_decode($_POST['cancelar']);
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("reservas", "where id = $reserva->id");
        header("Location: ../public/reservasCliente.php");
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