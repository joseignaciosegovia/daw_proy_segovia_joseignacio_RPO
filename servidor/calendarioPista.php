<?php
    session_start();

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/js/calendarioAdministrador.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de accesoAdministrador.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        // Guardamos el id de la pista
        $pista = $crud->obtener("pistas", "where nombre = \"$_GET[pista]\"")[0]['id'];

        $nombre = $crud->listar("nombre", "gestores", "where email = \"$_SESSION[gestor]\"")[0]['nombre'];

        // Guardamos las fechas ocupadas, el id de la pista y su nombre
        $reservasYPista = $crud->listar("*", "reservas", " WHERE pista = $pista");
        $reservasYPista[] = $pista;
        $reservasYPista[] = $_GET['pista'];
?>
        <h1 class="d-flex justify-content-center">Bienvenido/a <?php echo $nombre ?></h1>
        <h1 class="d-flex justify-content-center">Calendario de la pista <?php echo "$_GET[pista]" ?></h1>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
            </div>
        </div>
        <!-- Ocultamos esta sección porque solo se utilizará para pasar información a JavaScript -->
        <div id="calendario" hidden>
            <!-- Incluimos las fechas ocupadas y el nombre de la pista para que JavaScript pueda acceder a esta información -->
            <?php echo json_encode($reservasYPista) ?>
        </div>
        <a href="intranet.php"><button>Volver atrás</button></a>
<?php
    }

    else {
        header('Location: intranet.php');
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>