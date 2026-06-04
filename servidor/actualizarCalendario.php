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
    require_once __DIR__ . '/../vendor/autoload.php';
    use Clases\DB;
    // Importamos config.php para poder enviar correos
    require_once $_SERVER['DOCUMENT_ROOT'] . 'config.php';

    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..');
    $dotenv->safeLoad(); 

    // Si el administrador ha añadido una fecha ocupada o un cliente ha realizado una reserva
    if (isset($_POST['datos'])) {
        $datos = json_decode(($_POST['datos']));

        $crud = new Crud(new DB("proyecto"));
        // Si la reserva no tiene cliente
        if($datos->cliente == null) {
            $crud->insertarColumnas("reservas", "(fecha, horaInicio, horaFin, pista, cliente, informacion, idPago)", "\"$datos->fecha\", \"$datos->horaInicio\", \"$datos->horaFin\", \"$datos->id\", null, \"$datos->informacion\", \"No es una reserva\"");
        }
        else {
            $crud->insertarColumnas("reservas", "(fecha, horaInicio, horaFin, pista, cliente, informacion, idPago)", "\"$datos->fecha\", \"$datos->horaInicio\", \"$datos->horaFin\", \"$datos->id\", \"$datos->cliente\", \"$datos->informacion\", \"No pagada\"");
        }
    }

    // Si el administrador edita una reserva, ya sea desde consultarReservas o calendarioPista
    if(isset($_POST['Confirmar'])) {
        $editar = json_decode(($_POST['Confirmar']));
        $crud = new Crud(new DB("proyecto"));
        $reservaEditar = $crud->obtener("reservas", "where id = $editar->id")[0];
        $pistaReserva = $crud->obtener("pistas", "where id = $reservaEditar[pista]")[0];
        // Si venimos desde consultarReservas, la nueva reserva incluye la información
        if(property_exists($editar, "informacion"))
            $crud->actualizar("reservas", "fecha = \"$editar->fecha\", horaInicio = \"$editar->horaInicio\", horaFin = \"$editar->horaFin\", informacion = \"$editar->informacion\"", "where id = $editar->id");
        // Si venimos de calendarioPista, solo modificamos su fecha y su horario, pero no la información
        else
            $crud->actualizar("reservas", "fecha = \"$editar->fecha\", horaInicio = \"$editar->horaInicio\", horaFin = \"$editar->horaFin\"", "where id = $editar->id");

        // Si ese horario lo reservó un cliente, le enviamos un email informándole de la modificación
        if($reservaEditar['cliente'] != null) {
            $reservaNueva = $crud->obtener("reservas", "where id = $editar->id")[0];
            $cliente = $crud->obtener("clientes", "where email = \"$reservaNueva[cliente]\"")[0]['nombre'];
            // Enviamos un email al usuario para avisarle del cambio
            $cuerpo = ("
                <h2>Hola, $cliente</h2>
                <p>Debido a motivos imposibles de evitar, ha sido necesario modificar su reserva en horario $reservaEditar[fecha] a la hora $reservaEditar[horaInicio] en la pista $pistaReserva[nombre]</p>
                <p>Ahora la reserva tendrá lugar en la fecha $reservaNueva[fecha] a la hora $reservaNueva[horaInicio] (en la misma pista)</p>
                <p>Disculpe las molestias</p>
            ");

            $resultado = enviarCorreo($reservaEditar['cliente'], "Modificación de reserva", $cuerpo);
        }
    }

    // Si el administrador borra una reserva
    if(isset($_POST['Borrar'])) {
        $borrar = json_decode(($_POST['Borrar']));
        $crud = new Crud(new DB("proyecto"));
        $reservaEliminar = $crud->obtener("reservas", "where id = $borrar->id")[0];
        $pistaReserva = $crud->obtener("pistas", "where id = $reservaEliminar[pista]")[0];
        $crud->eliminar("reservas", "where id = $borrar->id");
        // Si ese horario lo reservó un cliente
        if($reservaEliminar['cliente'] != null) {
            // Si el cliente realizó el pago, se le devolverá el dinero
            if(!empty($reservaEliminar['idPago'])) {
                \Stripe\Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);

                try {
                    $refund = \Stripe\Refund::create([
                        'payment_intent' => $reservaEliminar['idPago'],
                    ]);
                    // Si ha habido algún fallo
                    if ($refund->status !== 'succeeded') {
                        http_response_code(500);
                        echo json_encode(['error' => 'La devolución no se ha podido procesar']);
                        exit();
                    }
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit();
                }
            }
            $cliente = $crud->obtener("clientes", "where email = \"$reservaEliminar[cliente]\"")[0]['nombre'];
            // Enviamos un email al cliente informándole de la cancelación
            $cuerpo = ("
                <h2>Hola, $cliente</h2>
                <p>Debido a motivos imposibles de evitar, ha sido necesario cancelar su reserva en horario $reservaEliminar[fecha] a la hora $reservaEliminar[horaInicio] en la pista $pistaReserva[nombre]</p>
                <p>Se le devolverá el dinero</p>
                <p>Disculpe las molestias</p>
            ");

            $resultado = enviarCorreo($reservaEliminar['cliente'], "Cancelación de reserva", $cuerpo);
        }
    }

    // Si el cliente cancela una reserva
    if(isset($_POST['cancelar'])) {
        $reserva = json_decode($_POST['cancelar']);
        $crud = new Crud(new DB("proyecto"));
        $reservaEliminar = $crud->obtener("reservas", "where id = $reserva->id")[0];

        // Si el cliente ha realizado el pago y tiene derecho a devolución
        if($reserva->devolverDinero && !empty($reservaEliminar['idPago'])) {
            \Stripe\Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);

            try {
                $refund = \Stripe\Refund::create([
                    'payment_intent' => $reservaEliminar['idPago'],
                ]);
                // Si ha habido algún fallo
                if ($refund->status !== 'succeeded') {
                    http_response_code(500);
                    echo json_encode(['error' => 'La devolución no se ha podido procesar']);
                    exit();
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
        }
        // Borramos la reserva
        $crud->eliminar("reservas", "where id = $reserva->id");
        header("Location: ../public/reservasCliente.php");
    }