<?php
    session_start();

    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    require_once __DIR__ . '/../vendor/autoload.php';
    use Clases\DB;

    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
    $dotenv->safeLoad(); 

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){ ?>
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
    <?php }

    $estado = $_GET['redirect_status'] ?? '';
    $idPago = $_GET['payment_intent'] ?? '';
    $idReserva = intval($_GET['idReserva'] ?? 0);

    $crud = new Crud(new DB("proyecto"));
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];

    if ($estado === 'succeeded' && $idReserva > 0 && $idPago !== '') {
        $reserva = $crud->obtener("reservas", "where id = $idReserva")[0] ?? null;

        // Si la reserva no existe o no pertenece al cliente logeado, vamos al inicio
        if (!$reserva || $reserva['cliente'] !== $_SESSION['cliente']) {
            header("Location: inicioCliente.php");
            exit();
        }

        // Si la reserva ya está ya pagada
        if ($reserva['estadoPago'] === 'pagado') {
            $estado = 'succeeded';
        // Si la reserva no está pagada
        } else {
            
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

            try {
                // Recuperamos el pago real desde la API de Stripe
                $paymentIntent = \Stripe\PaymentIntent::retrieve($idPago);

                $precioReserva = $crud->listar("precioReserva", "pistas", "where id = \"$reserva[pista]\"")[0]['precioReserva'];
                // Cruzamos los datos: mismo ID guardado en BD, mismo importe, mismo cliente
                $idPagoCoincide    = $reserva['idPago'] === $paymentIntent->id;
                $importeCorrecto   = $paymentIntent->amount === intval($precioReserva * 100);
                $estadoCorrecto    = $paymentIntent->status === 'succeeded';
                $metadataCorrecta  = isset($paymentIntent->metadata['id_reserva']) && intval($paymentIntent->metadata['id_reserva']) === $idReserva;

                // Si coinciden el id del pago, el importe el estado y los metadatos, actualizamos la reserva
                if ($idPagoCoincide && $importeCorrecto && $estadoCorrecto && $metadataCorrecta) {
                    $crud->actualizar("reservas", "estadoPago = \"Pagada\", idPago = \"$reserva[idPago]\"", "where id = $idReserva");
                // Si los datos no coinciden, algo ha fallado
                } else {
                    $estado = 'failed';
                }
            // Si ocurre cualquier error, marcamos el estado como fallido
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $estado = 'failed';
            }
        }
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
?>

<main class="main">
    <div class="card shadow-sm border-0 card-detalles">
        <div class="p-3 py-4">
        <?php if ($estado === 'succeeded'){ 
            
        ?>
            <div class="seccionSubtitulo mb-4">
                <i class="ti ti-thumb-up" aria-hidden="true"></i>
                <div>
                    <h1>¡Pago completado!</h1>
                    <small class="text-muted">Tu reserva ha sido confirmada correctamente.</small>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-2 text-start">
        <button class="btn btn-secondary" onclick="window.location.href='reservasCliente.php';">Consultar reservas</button>
    </div>
        <?php }
        else{ 
        ?>
            <div class="seccionSubtitulo mb-4">
                <i class="ti ti-face-id-error" aria-hidden="true"></i>
                <div>
                    <h1>El pago no se ha completado</h1>
                    <small class="text-muted">Por favor, inténtalo de nuevo.</small>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-2 text-start">
        <button class="btn btn-secondary" onclick="history.back();">Volver e intentarlo de nuevo</button>
    </div>
        <?php } ?>
</main>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php"; ?>