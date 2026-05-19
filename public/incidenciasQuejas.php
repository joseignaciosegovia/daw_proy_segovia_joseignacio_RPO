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

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Devuelve las iniciales de una cadena con distintas palabras
    function iniciales(string $nombre): string {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de "Enviar"
    if (isset($_POST['datos'])) {

        $datos = json_decode($_POST['datos']);
        $fecha = date('Y-m-d', time());

        // Añadimos la queja/sugerencia al perfil del usuario en la base de datos
        $crud->insertarColumnas("sugerencias_incidencias", "(fecha, contenido, cliente)", "\"$fecha\", \"$datos->contenido\", \"$_SESSION[cliente]\"");
    }

    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";

    // Datos que vamos a mostrar
    $fecha = new DateTime();
    // Formato de fecha en español
    $formatter = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE
    );
    $iniciales = iniciales($cliente['nombre']);
?>
            <!-- El contenido principal de la página será la segunda columna -->
            <main class="main">
                <div class="welcome-bar">
                    <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
                    <div class="welcome-text">
                        <h1>Bienvenida/o, <?php echo "$cliente[nombre]"; ?></h1>
                        <p>Hoy es <?php echo $formatter->format($fecha);?></p>
                    </div>
                    <span class="badge badge-green">
                        <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
                    </span>
                </div>
                <div class="card shadow-sm border-0">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="enviarIncidencias">
                        <div class="p-3 py-4">
                            <div class="section-header mb-4">
                                <i class="ti ti-mail" aria-hidden="true"></i>
                                <div>
                                    <h2>Sugerencias e incidencias</h2>
                                    <small class="text-muted">Envia sugerencias/incidencias</small>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <label for="quejaIncidencia" class="labels">Sugerencia o incidencia</label>
                                    <textarea class="form-control" id="quejaIncidencia" placeholder="Escribe aquí tu sugerencia o incidencia" name="Queja" rows="5" cols="100" required></textarea>
                                    <div class="invalid-feedback">
                                        Introduzca un mensaje
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-end">
                                <button class="btn btn-success px-4" type="submit" name="Enviar"><i class="ti ti-send me-2"></i>Realizar queja/sugerencia</button>
                            </div>
                        </div>
                    </form>
                </div>
<?php
    $sugerencias = $crud->listar("fecha, contenido", "sugerencias_incidencias", "where cliente = \"$_SESSION[cliente]\"");
    if($sugerencias == null) {
?>
        <div class="section-header mb-4">
            <i class="ti ti-circle-number-0" aria-hidden="true"></i>
            <div>
                <h2>No has realizado ninguna sugerencia/incidencia</h2>
                <small class="text-muted">Cuando realices sugerencias/incidencias podrás consultarlas aquí</small>
            </div>
        </div>
<?php
                }
                else{
?>
        <div class="card shadow-sm border-0">
            <div class="p-3 pt-4">
                <div class="section-header mb-4">
                    <i class="ti ti-history" aria-hidden="true"></i>
                    <div>
                        <h2>Historial de sugerencias/incidencias</h2>
                        <small class="text-muted">Consulta todas las sugerencias/incidencias enviadas anteriormente</small>
                    </div>
                </div>
                <table class="table table-hover">
                    <?php $contador = 1; ?>
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Contenido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sugerencias as $sugerencia){ ?>
                        <tr>
                            <td><?php echo $contador ?></td>
                            <td><?php echo $sugerencia['fecha'] ?></td>
                            <td><?php echo $sugerencia['contenido'] ?></td>
                            <?php $contador++; ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
        </main>
    </div>

    <?php
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    ?>