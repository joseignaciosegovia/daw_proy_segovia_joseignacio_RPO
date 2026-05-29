<?php
    session_start();

    // Si ya hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (!empty($_SESSION["cliente"])) {
        header("Location: inicioCliente.php");
        exit();
    }

    // Actualizamos el título de la página
    $titulo = "Login · Moral de Calatrava";
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

    // Función que guarda un mensaje de error (en caso de que haya habido algún problema) y actualiza la página
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: accesoCliente.php');
        exit;
    }

    // Si pulsamos en el botón "Acceder"
    if (isset($_POST['login'])) {
        $email = trim($_POST['usuario']);
        $contraseña = trim($_POST['pass']);

        $crud = new Crud(new DB("proyecto"));
        $acceso;
        $fecha = time();
        $fechaBloqueado = 0;

        // Borramos los registros que tengan más de diez minutos para limpiar la base de datos
        $crud->eliminar("conexiones", "where (hora + 600) < $fecha");

        // Si es la primera vez que intentamos acceder con este usuario
        if(!isset($_SESSION[$email])){
            $_SESSION[$email]['bloqueado'] = 0;
        }

        // Si el usuario con el que intentamos acceder está bloqueado
        if($_SESSION[$email]['bloqueado'] + 600 >= $fecha)
            error("Demasiados intentos erróneos con el usuario '$email'. No podrá iniciar sesión durante diez minutos");
        
        // Si el usuario no está bloqueado y el nombre de usuario o la contraseña son solo espacios en blanco
        if (strlen($email) == 0 || strlen($contraseña) == 0) {
            error("Error, El nombre o la contraseña no pueden contener solo espacios en blancos.");
        }

        // Comprobamos si existe un cliente con el usuario y la contraseña introducidos
        $cliente = $crud->isValido("clientes", $email, $contraseña);
        // Si no existe, mostramos el error y actualizamos la página
        if ($cliente == null) {
            $acceso = "Denegado";
            $crud->insertarColumnas("conexiones", "(usuario, hora, acceso)", "\"$email\", $fecha, \"$acceso\"");
            
            unset($_POST['login']);
            
            // Comprobamos si el usuario debería bloquearse
            $accesosIncorrectos = 0;
            $accesos = $crud->listar("*", "conexiones", " WHERE usuario = \"$email\" AND (hora + 180) >= $fecha ORDER BY hora DESC");
            
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
                    $_SESSION[$email]['bloqueado'] = $fechaBloqueado;
                    error("Demasiados intentos erróneos con el usuario '$email'. No podrá iniciar sesión en los próximos diez minutos");
                }
            }
            // Si el acceso es incorrecto pero no ha habido cinco accesos denegados en los últimos minutos
            error("Credenciales Inválidas");
        }
        // Si el acceso es correcto y el cliente no está validado
        if($cliente['activo'] == 0) {
            error("El cliente no está validado");
        }
        // Si el acceso es correcto y el cliente está validado
        else {
            $acceso = "Concedido";
            $crud->insertarColumnas("conexiones", "(usuario, hora, acceso)", "\"$email\", $fecha, \"$acceso\"");

            $_SESSION['cliente'] = $email;
            header('Location: inicioCliente.php');
            exit();
        }
    // Si no pulsamos el botón de acceder, mostramos la página
    } else {
        // Cargamos la cabecera
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
?>
        <main class="main container-fluid px-0 acceso" style="background:#e0e0e0;">
            <div class="d-flex justify-content-center h-100">
                <div class="p-3 py-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-center">
                            <h1>Iniciar sesión</h1>
                            <a class="btn btn-secondary" href="../index.php">Si no tienes cuenta, regístrate aquí</a>
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
                                    <input type="submit" value="Acceder" class="btn btn-success w-auto" name='login' id="btAccesoCliente">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                // Si ha habido algún error, lo mostramos antes que la información principal de la página
                if (isset($_SESSION['error'])) {
                    echo "<div class='mt-3 text-danger font-weight-bold text-lg text-center'>";
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