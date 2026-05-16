<?php
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como gestor o no obtenemos la variable "pista", volvemos a la página de inicio de la intranet
    if (empty($_SESSION["gestor"]) || !isset($_GET['pista'])) {
        header("Location: intranet.php");
        exit();
    }

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/js/calendarioAdministrador.js"></script>
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

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de accesoAdministrador.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));
        // Guardamos el gestor para que puedan mostrarse sus datos en la barra de navegación
        $gestor = $crud->obtener("gestores", "where email = \"$_SESSION[gestor]\"")[0];
        $fecha = new DateTime();
        // Formato de fecha en español
        $formatter = new IntlDateFormatter(
            'es_ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $iniciales = iniciales($gestor['nombre']);

        // Guardamos el id de la pista
        $pista = $crud->obtener("pistas", "where id = $_GET[pista]")[0]['nombre'];

        $nombre = $crud->listar("nombre", "gestores", "where email = \"$_SESSION[gestor]\"")[0]['nombre'];

        // Guardamos las fechas ocupadas, el id de la pista y su nombre
        $reservasYPista = $crud->listar("*", "reservas", " WHERE pista = $_GET[pista]");
        $reservasYPista[] = $_GET['pista'];
        $reservasYPista[] = $pista;
?>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <main class="container-fluid">
            <!-- BIENVENIDA -->
            <div class="welcome-bar">
                <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
                <div class="welcome-text">
                    <h1>Bienvenida/o, <?php echo "$gestor[nombre]"; ?></h1>
                    <p>Hoy es <?php echo $formatter->format($fecha);?> &middot; Usuario activo</p>
                </div>
                <span class="badge badge-green">
                    <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
                </span>
            </div>
        </main>
    </div>
    <!-- Ocultamos esta sección porque solo se utilizará para pasar información a JavaScript -->
    <div id="calendario" hidden>
        <!-- Incluimos las fechas ocupadas y el nombre de la pista para que JavaScript pueda acceder a esta información -->
        <?php echo json_encode($reservasYPista) ?>
    </div>
    <button class="btn btn-primary form-floating" onclick="window.location.href='intranet.php';">Volver atrás</button>
<?php
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>