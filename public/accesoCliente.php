<?php
    session_start();

    use Clases\DB;
    require_once "../controlador/Crud.php";
    require_once "../modelo/Conexion.inc.php";
    require_once "../modelo/Cliente.php";

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location:accesoCliente.php');
        die();
    }

    // Si ya hemos iniciado sesión como cliente, volvemos a la página de inicio
    /*if (!empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }*/

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
            integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
            integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <title>Login</title>
    </head>

    <body style="background:silver;">
    <?php
        if (isset($_POST['login'])) {
            $nombre = trim($_POST['usuario']);
            $contraseña = trim($_POST['pass']);

            $crud = new Crud(new DB("proyecto"));
            $acceso;
            $fecha = time();
            $fechaBloqueado = 0;

            // Si es la primera vez que intentamos acceder con este usuario
            if(!isset($_SESSION[$nombre])){
                $_SESSION[$nombre]['bloqueado'] = 0;
            }

            // Si el usuario con el que intentamos acceder está bloqueado
            if($_SESSION[$nombre]['bloqueado'] + 600 >= $fecha)
                error("Demasiados intentos erróneos con el usuario '$nombre'. No podrá iniciar sesión durante diez minutos");
            
            // Si el usuario no está bloqueado
            // Si el nombre de usuario o la contraseña son solo espacios en blanco
            if (strlen($nombre) == 0 || strlen($contraseña) == 0) {
                error("Error, El nombre o la contraseña no pueden contener solo espacios en blancos.");
            }

            // Comprobamos si existe un cliente con el usuario y la contraseña introducidos
            
            $cliente = $crud->isValido("clientes", $nombre, $contraseña);
            // Si no existe, mostramos el error y actualizamos la página
            if ($cliente == null) {
                $acceso = "Denegado";
                $crud->insertar("conexiones", "\"$nombre\", $fecha, \"$acceso\"");
                
                unset($_POST['login']);
                
                // Comprobamos si el usuario debería bloquearse
                $accesosIncorrectos = 0;
                $accesos = $crud->listar("conexiones", " WHERE usuario = \"$nombre\" AND (hora + 180) >= $fecha ORDER BY hora DESC");
                
                // Recorremos los accesos con este usuario en los últimos tres minutos empezando por los más recientes
                foreach($accesos as $acceso) {
                    // Si el acceso fue denegado, incrementamos el número de accesos incorrectos
                    if($acceso['acceso'] == "Denegado")
                        $accesosIncorrectos++;
                    // Si el acceso fue aceptado, dejamos de contar
                    else {
                        $accesosIncorrectos = 0;
                        break;
                    }
                    // Guardamos la fecha del intento más reciente porque será la fecha en que se bloquee el usuario
                    if($accesosIncorrectos == 1) 
                        $fechaBloqueado = $acceso['hora'];
                    // Si ha habido cinco accesos denegados seguidos bloqueamos al usuario guardando la fecha de bloqueo
                    else if($accesosIncorrectos == 5) {
                        $_SESSION[$nombre]['bloqueado'] = $fechaBloqueado;
                        error("Demasiados intentos erróneos con el usuario '$nombre'. No podrá iniciar sesión en los próximos diez minutos");
                    }
                }
                
                error("Credenciales Inválidas");
            }

            // Si el acceso es correcto
            $acceso = "Concedido";
            $crud->insertar("conexiones", "\"$nombre\", $fecha, \"$acceso\"");

            $_SESSION['cliente'] = $nombre;

            // MOSTRAR UN MENSAJE DE LOGEO CORRECTO Y QUE EL USUARIO PUEDA ACCEDER A SU INFORMACIÓN

            header('Location:../index.php');
        } else {
            ?>
            <div class="container mt-5">
                <div class="d-flex justify-content-center h-100">
                    <div class="card">
                        <div class="card-header">
                            <h3>Iniciar sesión</h3>
                            <h4>¿No tienes cuenta? <a href="registroCliente.php">Regístrate aquí</a></h4>
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
                                    <input type="submit" value="Login" class="btn float-right btn-success" name='login'>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
                if (isset($_SESSION['error'])) {
                    echo "<div class='mt-3 text-danger font-weight-bold text-lg'>";
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    echo "</div>";
                }
            ?>
        </div>
        <?php } ?>
    </body>
</html>