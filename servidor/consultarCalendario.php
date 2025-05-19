<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";
    
    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: intranet.php');
        die();
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_POST['salir'])) {
        unset($_SESSION['administrador']);
        header("Location: accesoAdministrador.php");
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de accesoAdministrador.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        $calendario = $crud->listar("*", "calendarios", " WHERE pista = \"$_GET[pista]\"");
?>

        <h1>Calendario de la pista <?php echo "$_GET[pista]" ?></h1>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once "../vista/template/navGestor.php"; ?>

                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col d-flex align-items-center">
                    <div id="calendario">
                        <?php echo "$_GET[pista]" ?>
                    </div>
                </div>
            </div>
        </div> 
        <a href="intranet.php"><button>Volver atrás</button></a>

        <!-- JQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="/proyecto/js/calendar.js"></script>
        <script src="/proyecto/js/daypilot-all.min.js"></script>
    </body>
</html>
<?php
    }
?>