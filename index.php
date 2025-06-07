<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <script type="module" src="/proyecto/js/validacion.js"></script>
<?php }

    // Función que guarda un mensaje de error (en caso de que haya habido algún problema) y actualiza la página
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: index.php');
        die();
    }

    // Función que comprueba si la cadena recibida está vacía
    function nombreNoVacio(&$nombre) {
        // Si el nombre del usuario está vacío, mostramos un error
        if (strlen($nombre) == 0) {
            error("Error el Nombre no puede estar en blanco");
        }

        // Ponemos la primera letra de cada palabra en mayúsculas
        $nombre = ucwords($nombre); 
    }

    // Si hemos iniciado sesión como cliente, mostramos la página de reservar pistas
    if (!empty($_SESSION["cliente"])) {
        header("Location: ./public/reservarPista.php");
        exit();
    }

    // Si pulsamos el botón "Crear Usuario"
    if (isset($_POST['datos'])) {
        // Recibimos los datos de JavaScript después de hacer la validación del submit
        $datos = json_decode($_POST['datos']);
        $crud = new Crud(new DB("proyecto"));

        // Trimamos las cadenas
        $nombre = trim($datos->nombre);
        $email = trim($datos->email);

        if($datos->telefono == null)
            $telefono =  0;
        else
            $telefono = $datos->telefono;

        // Comprobamos si el nombre del usuario está vacío
        nombreNoVacio($nombre);

        // Comprobamos si ya existe un usuario con el email introducido
        $respuesta = $crud->obtener("clientes", "where email = \"$email\"");
        if($respuesta != null) {
            error("El email está repetido");
        }

        // Comprobamos si la contraseña coincide con la confirmación de la contraseña
        if($datos->contraseña != $datos->confirmarContraseña){
            error("La contraseña tiene que coincidir");
        }

        // Guardamos en una variable la contraseña cifrada
        $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);
        // Insertamos el usuario en la base de datos
        $crud->insertar("clientes", "\"$email\", \"$contraseña\", \"$nombre\", $telefono");
        
        $_SESSION['mensaje'] = 'Cliente creado Correctamente';
        $_SESSION['cliente'] = $email;
    } else {
        // Si ha habido algún error, lo mostramos antes que la información principal de la página
        if (isset($_SESSION['error'])) {
            echo "<div class='mt-3 text-danger font-weight-bold text-lg d-flex justify-content-center'>";
            echo $_SESSION['error'];
            echo "</div>";
            // Borramos la variable para no volver a mostrar el error
            unset($_SESSION['error']);
        }
?>

    <div class="container-fluid my-3">
        <div class="row column-gap-3">
            <div class="col-12 col-md-5 pt-4" id="informacionPrincipal">
                <div class="row px-4">
                    <h2>Reservar pistas del polideportivo y de la Ciudad Deportiva</h2>
                    <p>En esta página podrás registrarte para reservar las pistas del polideportivo y de la Ciudad Deportiva de Moral de Calatrava</p>
                </div>
            </div>
            <div class="col-12 col-md-6">
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
                        <input type="email" class="form-control" id="email" placeholder="Correo electrónico" autocomplete="off" required>
                        <div class="invalid-feedback">
                            Introduzca un correo electrónico válido
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contraseña" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contraseña" placeholder="Contraseña" pattern=".{8,}" required>
                        <div id="passwordHelpBlock" class="form-text">
                            La contraseña debe tener al menos 8 caracteres 
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
                            La contraseña coincide
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
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/footer.php";
}
?>