<?php
    session_start();

    // Si no hemos iniciado sesión ni como gestor ni como cliente, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"]) && empty($_SESSION["cliente"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si hemos llegado a esta página por otros medios (por ejemplo, escribiendo la dirección directamente)
    if(!isset($_POST['datos']) && !isset($_POST['Confirmar']) && !isset($_POST['Borrar']) && !isset($_POST['cancelar'])) {
        // Si hemos iniciado sesión como gestor, vamos a la página principal de los gestores
        if(!empty($_SESSION["gestor"])){
            header("Location: intranet.php");
            exit();
        }
        // Si hemos iniciado sesión como cliente, vamos a la página principal de los clientes
        if(!empty($_SESSION["cliente"])){
            header("Location: ../index.php");
            exit();
        }
    }
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
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

    // Si el administrador edita una reserva, ya sea desde consultarReservas o calendarioPista
    if(isset($_POST['Confirmar'])) {
        $editar = json_decode(($_POST['Confirmar']));
        $crud = new Crud(new DB("proyecto"));
        $reservaEditar = $crud->obtener("reservas", "where id = $editar->id")[0];
        $pistaReserva = $crud->obtener("pistas", "where id = $editar->id");
        // Si venimos desde consultarReservas, la nueva reserva incluye la información
        if(property_exists($editar, "informacion"))
            $crud->actualizar("reservas", "fecha = \"$editar->fecha\", horaInicio = \"$editar->horaInicio\", horaFin = \"$editar->horaFin\", informacion = \"$editar->informacion\"", "where id = $editar->id");
        // Si venimos de calendarioPista, solo modificamos su fecha y su horario, pero no la información
        else
            $crud->actualizar("reservas", "fecha = \"$editar->fecha\", horaInicio = \"$editar->horaInicio\", horaFin = \"$editar->horaFin\"", "where id = $editar->id");

        $reservaNueva = $crud->obtener("reservas", "where id = $editar->id")[0];
        // Se envía un email al usuario para avisarle del cambio
        $body = json_encode([
            'from'    => 'onboarding@resend.dev',
            'to'      => [$reservaEditar->email],
            'subject' => 'Verifica tu cuenta',
            'html'    => "
                <h2>Hola, $email</h2>
                <p>Por diversos motivos, ha sido necesario modificar su reserva en horario $reservaEditar->fecha a la hora $reservaEditar->horaInicio en la pista $pistaReserva->nombre</p>
                <p>Ahora la reserva tendrá la fecha $reservaNueva->fecha a la hora $reservaNueva->horaInicio (en la misma pista)</p>
            ",
        ]);

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . RESEND_API_KEY,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }

    // Si el administrador borra una reserva
    if(isset($_POST['Borrar'])) {
        $borrar = json_decode(($_POST['Borrar']));
        $crud = new Crud(new DB("proyecto"));
        $reservaEliminar = $crud->obtener("reservas", "where id = $borrar->id")[0];
        $pistaReserva = $crud->obtener("pistas", "where id = $borrar->id");
        $crud->eliminar("reservas", "where id = $borrar->id");
        // Si ese horario lo reservó un cliente, le enviamos un email informándole de la cancelación
        if($reservaEliminar->cliente != "null") {
            // Cuerpo del email
            $body = json_encode([
                'from'    => 'onboarding@resend.dev',
                'to'      => [$reservaEliminar->email],
                'subject' => 'Verifica tu cuenta',
                'html'    => "
                    <h2>Hola, $reservaEliminar->email</h2>
                    <p>Por diversos motivos, ha sido necesario cancelar su reserva en horario $reservaEliminar->fecha a la hora $reservaEliminar->horaInicio en la pista $pistaReserva->nombre</p>
                ",
            ]);

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . RESEND_API_KEY,
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
    }

    // Si el cliente cancela una reserva
    if(isset($_POST['cancelar'])) {
        $reserva = json_decode($_POST['cancelar']);
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("reservas", "where id = $reserva->id");
        header("Location: ../public/reservasCliente.php");
    }