<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";
    
    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    function añadirScriptsCabecera(){
?>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
<?php }

    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script src="/proyecto/js/calendarioAdministrador.js"></script>
<?php }

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: intranet.php');
        die();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de accesoAdministrador.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        // Guardamos las fechas ocupadas y el nombre de la pista
        $reservasYPista = $crud->listar("*", "reservas", " WHERE pista = \"$_GET[pista]\"");
        $reservasYPista[] = $_GET['pista'];
?>

        <h1>Calendario de la pista <?php echo "$_GET[pista]" ?></h1>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once "../vista/template/navGestor.php"; ?>
            </div>
        </div> 
        <div id="calendario">
            <!-- Incluimos las fechas ocupadas de esta pista para que JavaScript pueda acceder a ellas -->
            <?php echo json_encode($reservasYPista) ?>
        </div>
        
        <a href="intranet.php"><button>Volver atrás</button></a>
<?php
    }

    else {
        header('Location: intranet.php');
        die();
    }

    require_once "../vista/template/footer.php";
?>