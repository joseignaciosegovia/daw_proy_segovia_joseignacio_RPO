<?php
    ob_start(); // activa el buffer
    session_start();

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <script type="module" src="/js/confirmacion.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si pulsamos el botón de actualizar
    if (isset($_POST['Actualizar'])) {
        $datos = json_decode($_POST['datos']);

        $valores = "email = \"$_POST[Email]\", contrasena = '$_POST[Contraseña]'";        
        $condicion = "where email = \"$_POST[emailOriginal]\"";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("gestores", $valores, $condicion);

        $_GET['gestor'] = $_POST['email'];
    }

    // Si pulsamos el botón de borrar
    elseif (isset($_POST['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("gestores", "where email = \"$_POST[Email]\"");
        // En confirmacion.js está el mensaje para confirmar el borrado
        header("Location: intranet.php");
    }

    // Si se obtiene la variable "gestor" (pulsando el botón "Editar gestor" de intranet.php)
    if(isset($_GET['gestor'])) {
        $crud = new Crud(new DB("proyecto"));
        $gestor = $crud->obtener("gestores", "where email = \"$_GET[gestor]\"")[0];
    
?>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8 d-flex align-items-center">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?gestor=" . $gestor['email']; ?>" name="editarGestor">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Gestor: <?php echo "$_GET[gestor]" ?></h4>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Email</label>
                                <input type="email" class="form-control" id="email" name="Email" value="<?php echo $gestor['email'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un correo válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="labels">Contraseña</label>
                                <input type="password" class="form-control" id="contraseña" name="Contraseña" value="<?php echo $gestor['contrasena'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca una contraseña válida
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar gestor</button>
                            <button class="btn btn-danger profile-button" name="Borrar">Borrar gestor</button>
                        </div>
                        <!-- Campo oculto para guardar el email del gestor antes de actualizarlo -->
                        <input id="emailOriginal" name="emailOriginal" type="hidden" value="<?php echo "$gestor[email]"?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="intranet.php"><button>Volver atrás</button></a>

<?php
    }
    // Si no hemos llegado a esta página a través del botón "Editar gestor" de intranet.php, volvemos a dicha página
    else {
        header('Location: intranet.php');
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>