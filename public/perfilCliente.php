<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <script type="module" src="/js/validacion.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

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

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de actualizar perfil
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        // Trimamos el nombre
        $nombre = ucwords($datos->nombre);

        if($datos->telefono == null)
            $telefono =  0;
        else
            $telefono = $datos->telefono;

        // Comprobamos si el nombre del usuario está vacío
        nombreNoVacio($nombre);

        $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);

        $valores = "nombre = \"$nombre\", telefono = $telefono, contraseña = \"$contraseña\"";
        $condicion = "where email = \"$_SESSION[cliente]\"";

        // Actualizamos el perfil en la base de datos
        $crud->actualizar("clientes", $valores, $condicion);
    }
?>

<?php
    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";

    // Si ha habido algún error, lo mostramos antes que la información principal de la página
    if (isset($_SESSION['error'])) {
        echo "<div class='mt-3 text-danger font-weight-bold text-lg d-flex justify-content-center'>";
        echo $_SESSION['error'];
        echo "</div>";
        // Borramos la variable para no volver a mostrar el error
        unset($_SESSION['error']);
    }
?>

    <!-- El contenido principal de la página será la segunda columna -->
    <div class="col-8 col-sm-6 d-flex align-items-center">
        <?php
            $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
        ?>
        
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="perfilCliente">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Perfil</h4>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 col-lg-7">
                        <label class="labels">Nombre</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre" name="Nombre" value="<?php echo $cliente['nombre'] ?>" required>
                        <div class="invalid-feedback">
                            Introduzca un nombre
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 col-lg-7">
                        <label class="labels">Contraseña</label>
                        <input type="password" class="form-control" id="contraseña" placeholder="Contraseña" name="Contraseña" value="" pattern=".{8,}">
                        <div class="invalid-feedback">
                            Introduzca una contraseña válida
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-7">
                        <label class="labels">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="confirmarContraseña" placeholder="Contraseña" name="Confirmar contraseña" value="">
                        <div class="invalid-feedback">
                            Confirme la contraseña
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-10 col-sm-7 col-md-5 col-lg-4 col-xl-3">
                            <label class="labels">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" placeholder="Teléfono" name="Telefono" value="<?php echo $cliente['telefono'] ?>">
                            <div class="invalid-feedback">
                                Introduzca un número de teléfono válido
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar perfil</button></div>
            </div>
        </form>
    </div>
</div>
    
<?php
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>