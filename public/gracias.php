<?php
    session_start();

    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){ ?>
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
    <?php }

    $status = $_GET['redirect_status'] ?? '';
    $paymentIntent = $_GET['payment_intent'] ?? '';
    $crud = new Crud(new DB("proyecto"));
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
?>

<main class="main">
    <div class="card shadow-sm border-0 card-detalles">
        <div class="p-3 py-4">
        <?php if ($status === 'succeeded'){ 
        ?>
            <div class="seccionSubtitulo mb-4">
                <i class="ti ti-thumb-up" aria-hidden="true"></i>
                <div>
                    <h2>¡Pago completado!</h2>
                    <small class="text-muted">Tu reserva ha sido confirmada correctamente.</small>
                    <p><small>Referencia: <?= htmlspecialchars($paymentIntent) ?></small></p>
                </div>
            </div>
        <?php }
        else{ 
        ?>
            <div class="seccionSubtitulo mb-4">
                <i class="ti ti-face-id-error" aria-hidden="true"></i>
                <div>
                    <h2>El pago no se ha completado</h2>
                    <small class="text-muted">Por favor, inténtalo de nuevo.</small>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
    <div class="mt-2 text-start">
        <button class="btn btn-secondary" onclick="window.location.href='reservasCliente.php';">Consultar reservas</button>
    </div>
</main>

</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php"; ?>