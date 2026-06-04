<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    ob_start(); // activa el buffer
    session_start();

    // Si ya hemos iniciado sesión como gestor, redirigimos a la página de gestión
    if (!empty($_SESSION["gestor"])) {
        header("Location: intranet.php");
        exit();
    }

    // Función que muestra un mensaje de error (en caso de que haya habido algún problema) y actualiza la página
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: accesoAdministrador.php');
        die();
    }

    // Actualizamos el título de la página
    $titulo = "Login Intranet · Moral de Calatrava";
    
    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/modelo/Conexion.inc.php";  

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <!-- Bootstrap CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<?php }

    // Si pulsamos el botón de "Acceder
    if (isset($_POST['login'])) {
        // Trimamos los datos
        $email = trim($_POST['usuario']);
        $contraseña = trim($_POST['pass']);

        $crud = new Crud(new DB("proyecto"));
        // Comprobamos si existe un gestor con el usuario y la contraseña introducidos
        $gestor = $crud->isValido("gestores", $email, $contraseña);
        // Si no existe el gestor
        if ($gestor == null) {
            unset($_POST['login']);
            error("Credenciales Inválidas");
        }
        // Si existe el gestor
        else {
            $_SESSION['gestor'] = $email;
            // Si el gestor también es administrador
            if($gestor['administrador'] == 1)
                $_SESSION['administrador'] = $email;
            // Si el gestor no es administrador
            else
                $_SESSION['administrador'] = null;
            header('Location: intranet.php');
        }
    // Si no pulsamos el botón de "Acceder", mostramos la página
    } else {
        // Cargamos la cabecera
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
?>
        <main class="main container-fluid px-0 acceso" style="background:#e0e0e0;">
            <div class="d-flex justify-content-center h-100">
                <div class="p-3 py-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-center">
                            <h1>Iniciar sesión como gestor</h1>
                            <a class="btn btn-secondary w-auto" href="../index.php">Página de inicio</a>
                        </div>
                        <div class="card-body">
                            <form name='login' method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="email" class="form-control" placeholder="email" name='usuario' required>
                                </div>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control" placeholder="contraseña" name='pass' required>
                                </div>
                                <div class="form-group mb-3 text-right">
                                    <input type="submit" value="Acceder" class="btn btn-success w-auto" name='login' id="btAccesoGestor">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                // Si ha habido algún error, lo mostramos antes que la información principal de la página
                if (isset($_SESSION['error'])) {
                    echo "<div class='mt-3 text-danger font-weight-bold text-lg d-flex justify-content-center'>";
                    echo $_SESSION['error'];
                    echo "</div>";
                    // Borramos la variable para no volver a mostrar el error
                    unset($_SESSION['error']);
                }
            ?>
        </main>
<?php } 
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>