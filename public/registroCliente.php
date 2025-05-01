<?php
    session_start();

    use Clases\Cliente;
    use Clases\DB;
    require_once "../controlador/Crud.php";
    require_once "../modelo/Conexion.inc.php";
    require_once "../modelo/Cliente.php";

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location:registroCliente.php');
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
        <meta name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- css para usar Bootstrap -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
            integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
            integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <title>Crear usuario</title>
    </head>
    <body>
    <?php
        function nombreNoVacio(&$nombre) {
            // Comprobamos que el nombre no esté vacío
            if (strlen($nombre) == 0) {
                error("Error el Nombre no puede estar en blanco");
            }

            // Ponemos la primera letra de cada palabra en mayúsculas
            $nombre = ucwords($nombre); 
        }
        
        // Si pulsamos el botón "Crear"
        if (isset($_POST['enviar'])) {

            $crud = new Crud(new DB("proyecto"));

            // Recogemos los datos del formulario
            // Trimamos las cadenas
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);

            if($_POST['telefono'] == null)
                $telefono =  0;
            else
                $telefono = $_POST['telefono'];

            nombreNoVacio($nombre);

            $respuesta = $crud->obtener("clientes", "email = \"$email\"");
            if($respuesta != null) {
                error("El email está repetido");
            }

            if($_POST['contraseña'] != $_POST['contraseña2']){
                error("La contraseña tiene que coincidir");
            }

            $cliente = new Cliente($email, password_hash($_POST['contraseña'], PASSWORD_DEFAULT), $nombre, $telefono);

            $crud->insertar("clientes", "\"$cliente->email\", \"$cliente->contraseña\", \"$cliente->nombre\", $cliente->telefono");

            // Ventana que indica que el cliente se ha creado correctamente
            // NO MUESTRA LA VENTANA
            echo "<dialog open>
              <p>El perfil se ha actualizado correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
            </dialog>";
            
            $_SESSION['mensaje'] = 'Cliente creado Correctamente';

            header('Location:../index.php');
        } else {
    ?>
        <div class="container mt-5">
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header">
                        <h3>Crear Usuario</h3>
                        <h4>¿Tienes cuenta? <a href="accesoCliente.php">Inicia sesión aquí</a></h4>
                    </div>
                    <div class="card-body">
                        <form name="crear" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="mb-3">
                                <label for="nom">Nombre Completo</label>
                                <input type="text" class="form-control" id="nom" placeholder="Nombre Completo" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="ema">Email</label>
                                <input type="email" class="form-control" id="ema" placeholder="Email" name="email" required>
                            <div class="mb-3">
                                <label for="con">Contraseña</label>
                                <input type="password" class="form-control" id="con" placeholder="Contraseña" name="contraseña" required>
                            </div>
                            <div class="mb-3">
                                <label for="con">Repita la contraseña</label>
                                <input type="password" class="form-control" id="con2" placeholder="Contraseña" name="contraseña2" required>
                            </div>
                            <div class="mb-3">
                                <label for="tel">Teléfono</label>
                                <input type="tel" class="form-control" id="tel" placeholder="Teléfono" name="telefono" pattern="[0-9]{9}">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary mr-3" name="enviar">Crear</button>
                                <input type="reset" value="Limpiar" class="btn btn-success mr-3">
                            </div>
                        </form>
                    </div>
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
        <?php } ?>
    </body>
</html>