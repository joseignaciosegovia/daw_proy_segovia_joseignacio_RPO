<?php
    ob_start(); // activa el buffer
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/modelo/Conexion.inc.php";

    // Función que muestra un mensaje de error (en caso de que haya habido algún problema) y actualiza la página
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: accesoAdministrador.php');
        die();
    }

    // Si ya hemos iniciado sesión como administrador, redirigimos a la página de gestión
    if (!empty($_SESSION["administrador"])) {
        header("Location: intranet.php");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <title>Acceso a la gestión</title>
    </head>

    <body style="background:silver;">
    <?php
        // Si pulsamos el botón de "Acceder
        if (isset($_POST['login'])) {
            $nombre = trim($_POST['usuario']);
            $contraseña = trim($_POST['pass']);

            $crud = new Crud(new DB("proyecto"));

            // Comprobamos si existe un administrador con el usuario y la contraseña introducidos
            
            $administrador = $crud->isValido("gestores", $nombre, $contraseña);
            // Si no existe, mostramos el error y actualizamos la página
            if ($administrador == null) {
                unset($_POST['login']);
                error("Credenciales Inválidas");
            }

            // Si el acceso es correcto
            $_SESSION['administrador'] = $nombre;
            header('Location: intranet.php');
        } else {
    ?>
            <div class="container mt-5">
                <div class="d-flex justify-content-center h-100">
                    <div class="card">
                        <div class="card-header">
                            <h3>Iniciar sesión como administrador</h3>
                        </div>
                        <div class="card-body">
                            <form name='login' method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="usuario" name='usuario' required>
                                </div>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control" placeholder="contraseña" name='pass' required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Acceder" class="btn float-right btn-success" name='login'>
                                </div>
                            </form>
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
        </div>
        <?php } ?>
    </body>
</html>