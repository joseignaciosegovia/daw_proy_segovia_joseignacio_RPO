<?php
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Si no hemos recibido los datos de la reserva desde calendarioCliente.js, vamos a la página de inicio
    if(!isset($_GET['datos'])){
        header("Location: inicioCliente.php");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;
    require_once __DIR__ . '/../vendor/autoload.php';

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src="https://js.stripe.com/v3/"></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/js/calendarioSinCliente.js"></script>
        <script src="/js/pasarela.js"></script>
<?php }

    // Devuelve las iniciales de una cadena con distintas palabras
    function iniciales(string $nombre): string {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                // Para cada palabra, nos quedamos con la primera letra y la transformamos a mayúscula
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    // Si hemos recibido los datos de la reserva desde calendarioCliente.js
    if(isset($_GET['datos'])){
        $datosReserva = json_decode(($_GET['datos']));
        $crud = new Crud(new DB("proyecto"));
        // Guardamos el cliente para que puedan mostrarse sus datos en la barra de navegación
        $cliente = $crud->obtener("clientes", "where email = \"$datosReserva->cliente\"")[0];
        $precio = $crud->listar("precioReserva", "pistas", "where nombre = \"$datosReserva->pista\"")[0]['precioReserva'];
        $idReserva = $crud->obtener("reservas", "where cliente = \"$datosReserva->cliente\" and pista = $datosReserva->id and fecha = \"$datosReserva->fecha\" and horaInicio = \"$datosReserva->horaInicio\"")[0]['id'];

        $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..');
        $dotenv->safeLoad(); 
        \Stripe\Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $precio * 100, // precio en céntimos
                'currency' => 'eur',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => ['id_reserva' => (string)$idReserva],
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        // Guardamos el id del pago en la reserva para poder verificarlo después
        $crud->actualizar("reservas", "idPago = \"$paymentIntent->id\"", "where id = $idReserva");
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";

        // Datos que vamos a mostrar
        $fecha = new DateTime();
        // Formato de fecha en español
        $formatoFecha = new IntlDateFormatter(
            // fecha en español
            'es_ES',
            // Formato Martes, 12 de abril de 1952 d. C. o 15:30:42 h (hora del Pacífico)
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        // Guardamos las iniciales del nombre completo del usuario
        $iniciales = iniciales($cliente['nombre']);

?>
    <main class="main">
        <!-- BIENVENIDA -->
        <div class="welcome-bar">
            <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
            <div class="welcome-text">
                <h1>Bienvenida/o, <?php echo "$cliente[nombre]"; ?></h1>
                <p>Hoy es <?php echo $formatoFecha->format($fecha);?></p>
            </div>
            <span class="badge badge-green">
                <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
            </span>
        </div>
        <!-- DETALLES RESERVA -->
        <div class="card shadow-sm border-0 card-detalles">
            <div class="p-3 py-4">
                <div class="seccionSubtitulo mb-4">
                    <i class="ti ti-plus" aria-hidden="true"></i>
                    <div>
                        <h2>Detalles de la reserva</h2>
                        <small class="text-muted">Consulta los datos de la reserva que acabas de realizar</small>
                    </div>
                </div>
                <div class="accordion accordion-flush">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Pista</th>
                                    <th>Fecha</th>
                                    <th>Hora de inicio</th>
                                    <th>Hora de fin</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $datosReserva->pista ?></td>
                                    <td><?php echo $datosReserva->fecha ?></td>
                                    <td><?php echo $datosReserva->horaInicio ?></td>
                                    <td><?php echo $datosReserva->horaFin ?></td>
                                    <td><?php echo $precio ?> €</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
       <!-- PASARELA DE PAGO --> 
        <div class="card shadow-sm border-0 card-detalles">
            <div class="p-3 py-4">
                <div class="seccionSubtitulo mb-4">
                    <i class="ti ti-credit-card-pay" aria-hidden="true"></i>
                    <div>
                        <h2>Pago de la reserva</h2>
                        <small class="text-muted">Realice el pago de la reserva</small>
                    </div>
                </div>
                <div id="payment-element"></div>
                <button class="btn btn-success" id="submit" data-secret="<?php echo $paymentIntent->client_secret; ?>" data-id="<?php echo $idReserva; ?>">Pagar</button>
                <div id="error-message"></div>
            </div>
        </div>
    </main>
    <!-- Cerramos la sección principal, creada en navCliente.php -->
</div>

<?php
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    }
?>