<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
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
                // Para cada palabra, nos quedamos con la primera letra y la transformamos a mayúscula
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
    // Guardamos el cliente para que puedan mostrarse sus datos en la barra de navegación
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
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
        <div class="card shadow-sm border-0">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="enviarIncidencias">
                <div class="p-3 py-4">
                    <div class="seccionSubtitulo mb-4">
                        <i class="ti ti-mail" aria-hidden="true"></i>
                        <div>
                            <h2>Sugerencias e incidencias</h2>
                            <small class="text-muted">Envía sugerencias/incidencias</small>
                        </div>
                    </div>
                    <div>
                        <div>
                            <label for="quejaIncidencia" class="labels">Sugerencia o incidencia</label>
                            <textarea style="background: #E0E0E0" class="form-control" id="quejaIncidencia" placeholder="Escribe aquí tu sugerencia o incidencia" name="Queja" rows="5" cols="100" required></textarea>
                            <div class="invalid-feedback">
                                Introduzca un mensaje
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button class="btn btn-success px-4" type="submit" name="Enviar">Realizar queja/sugerencia</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card shadow-sm border-0">
            <div class="p-3 pt-4">
<?php
    // Variables relacionadas con la tabla de incidencias
    $filasPorPagina = 10;
    // Por defecto, estaremos en la página 1
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $desplazamiento = ($pagina - 1) * $filasPorPagina;

    $filasTotales = $crud->listar("count(*)", "sugerencias_incidencias", "where cliente = \"$_SESSION[cliente]\"")[0]['count(*)'];
    // Redondeamos al número superior para saber cuántas páginas tendrá la tabla
    $totalPaginas = ceil($filasTotales / $filasPorPagina);
    $paginaSiguiente = $pagina + 1;
    $paginaAnterior = $pagina - 1;

    // Obtenemos tantas incidencias como filas por página, empezando por la que toque para la página en la que nos encontremos
    $sugerencias = $crud->listar("*", "sugerencias_incidencias", "where cliente = \"$_SESSION[cliente]\" ORDER BY fecha ASC LIMIT $filasPorPagina OFFSET $desplazamiento");

    // Si el usuario no ha enviado ninguna incidencia
    if($sugerencias == null) {
?>
                <div class="seccionSubtitulo mb-4">
                    <i class="ti ti-circle-number-0" aria-hidden="true"></i>
                    <div>
                        <h2>No has realizado ninguna sugerencia/incidencia</h2>
                        <small class="text-muted">Cuando realices sugerencias/incidencias podrás consultarlas aquí</small>
                    </div>
                </div>
<?php
    }
    // Si el usuario ha enviado alguna incidencia
    else{
?>
                <div class="seccionSubtitulo mb-4">
                    <i class="ti ti-history" aria-hidden="true"></i>
                    <div>
                        <h2>Historial de sugerencias/incidencias</h2>
                        <small class="text-muted">Consulta todas las sugerencias/incidencias enviadas anteriormente</small>
                    </div>
                </div>
<?php
        // Si nos encontramos en una página que no sea la primera, mostramos una opción para volver a la página anterior
        if ($pagina > 1){
            echo "<a href=\"?pagina=$paginaAnterior\">← Anterior</a> ";
        }
        // Mostramos la página en la que nos encontramos
        echo "<span>Página $pagina de $totalPaginas></span>";
        // Si no estamos en la última página, mostramos una opción para ir a la siguiente
        if ($pagina < $totalPaginas) {
            echo " <a href=\"?pagina=$paginaSiguiente\">Siguiente →</a>";
        }
?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap">
                        <?php $contador = 1; ?>
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Contenido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Recorremos las incidencias -->
                            <?php foreach($sugerencias as $sugerencia){ ?>
                            <tr>
                                <td><?php echo $contador ?></td>
                                <td><?php echo $sugerencia['fecha'] ?></td>
                                <!-- Ajustamos el ancho de la última columna al contenido con style -->
                                <td style="width: 1%; white-space: nowrap;"><?php echo $sugerencia['contenido'] ?></td>
                                <?php $contador++; ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
    <?php } ?>
            </div>
        </div>
    </main>
    <!-- Cerramos la sección principal, creada en navCliente.php -->
</div>
<?php
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>