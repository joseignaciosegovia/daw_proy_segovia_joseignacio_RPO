<?php
    session_start();

    use Clases\Cliente;
    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/modelo/Cliente.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    // Si hemos iniciado sesión como cliente, mostramos la página de resrevar pistas
    if (!empty($_SESSION["cliente"])) {
        header("Location: ./public/reservarPista.php");
        exit();
    }

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location:index.php');
        die();
    }

    function nombreNoVacio(&$nombre) {
        // Comprobamos que el nombre no esté vacío
        if (strlen($nombre) == 0) {
            error("Error el Nombre no puede estar en blanco");
        }

        // Ponemos la primera letra de cada palabra en mayúsculas
        $nombre = ucwords($nombre); 
    }
    
    // Si pulsamos el botón "Crear Usuario"
    if (isset($_POST['datos'])) {

        $datos = json_decode($_POST['datos']);

        $crud = new Crud(new DB("proyecto"));

        // Recogemos los datos del formulario
        // Trimamos las cadenas
        //$nombre = trim($_POST['nombre']);
        //$email = trim($_POST['email']);

        $nombre = trim($datos->nombre);
        $email = trim($datos->email);

        /*if($_POST['telefono'] == null)
            $telefono =  0;
        else
            $telefono = $_POST['telefono'];*/

        if($datos->telefono == null)
            $telefono =  0;
        else
            $telefono = $datos->telefono;

        nombreNoVacio($nombre);

        $respuesta = $crud->obtener("clientes", "where email = \"$email\"");
        if($respuesta != null) {
            error("El email está repetido");
        }

        if($datos->contraseña != $datos->confirmarContraseña){
            error("La contraseña tiene que coincidir");
        }

        //$cliente = new Cliente($email, password_hash($_POST['contraseña'], PASSWORD_DEFAULT), $nombre, $telefono);
        $cliente = new Cliente($email, password_hash($datos->contraseña, PASSWORD_DEFAULT), $nombre, $telefono);

        $crud->insertar("clientes", "\"$cliente->email\", \"$cliente->contraseña\", \"$cliente->nombre\", $cliente->telefono");
        
        $_SESSION['mensaje'] = 'Cliente creado Correctamente';
        $_SESSION['cliente'] = $email;

        // Ventana que indica que el perfil se ha actualizado correctamente
?>

        
        <dialog open>
              <p>El cliente se ha actualizado correctamente</p>
            <button onclick="this.parentElement.close()">OK</button>
        </dialog>
<?php

        header('Location:public/reservarPista.php');
    } else {
?>

    <div class="container-fluid my-3">
        <div class="row column-gap-3">
            <div class="col pt-4" id="informacionPrincipal">
                <div class="row px-4">
                    <h2>Reservar pistas del polideportivo y de la Ciudad Deportiva</h2>
                    <p>En esta página podrás registrarte para reservar las pistas del polideportivo y de la Ciudad Deportiva de Moral de Calatrava</p>
                </div>
            </div>
            <div class="col">
                <h2 class="d-flex justify-content-center">Registrarse</h2>
                <a class="btn btn-secondary my-2 d-flex justify-content-center" href="public/accesoCliente.php">Si ya tienes cuenta, inicia sesión aquí</a>
                
                <form class="row needs-validation px-4" name="crearUsuario" novalidate>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre Completo" required>
                        <div class="invalid-feedback">
                            Introduzca un nombre
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" placeholder="Correo electrónico" required>
                        <div class="invalid-feedback">
                            Introduzca un correo electrónico válido
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contraseña" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contraseña" placeholder="Contraseña" required>
                        <div id="passwordHelpBlock" class="form-text">
                            La contraseña debe cumplir los siguientes requisitos: 
                        </div>
                        <div class="invalid-feedback">
                            Introduzca una contraseña válida
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmarContraseña" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="confirmarContraseña" placeholder="Contraseña" required>
                        <div class="invalid-feedback">
                            Confirme la contraseña
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" placeholder="Teléfono (opcional)" pattern="[0-9]{9}">
                        <div class="invalid-feedback">
                            Introduzca un número de teléfono válido
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary form-floating" name="crear">Crear Usuario</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    

    <?php
        // Cargamos el pie
        require_once "vista/template/footer.php";
    }
    ?>