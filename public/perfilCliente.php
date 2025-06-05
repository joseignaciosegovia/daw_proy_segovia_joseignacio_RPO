<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    function añadirScriptsCabecera(){
?>
        <script type="module" src="/proyecto/js/validacion.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    $crud = new Crud(new DB("proyecto"));

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Si pulsamos el botón de actualizar perfil
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        if($datos->telefono == null)
            $telefono =  0;
        else
            $telefono = $datos->telefono;

        $cliente = [
            "nombre" => $datos->nombre,
            "contraseña" => password_hash($datos->contraseña, PASSWORD_DEFAULT),
            "telefono" => $telefono
        ];

        $valores = "nombre = \"$cliente[nombre]\", telefono = $cliente[telefono], contraseña = \"$cliente[contraseña]\"";
        $condicion = "where email = \"$_SESSION[cliente]\"";

        // Actualizamos el perfil en la base de datos
        $crud->actualizar("clientes", $valores, $condicion);
    }
?>

<?php
    // Cargamos la cabecera
    require_once "../vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
    require_once "../vista/template/navCliente.php";
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
    require_once "../vista/template/footer.php";
?>