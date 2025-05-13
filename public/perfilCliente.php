<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: perfilCliente.php');
        die();
    }

    $crud = new Crud(new DB("proyecto"));

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Si pulsamos el botón de actualizar perfil
    if (isset($_POST['Actualizar'])) {
        $cliente = [
            "nombre" => $_POST['Nombre'],
            "contraseña" => password_hash($_POST['Contraseña'], PASSWORD_DEFAULT),
            "telefono" => $_POST['Telefono']
        ];

        $valores = "nombre = \"$cliente[nombre]\", telefono = $cliente[telefono], contraseña = \"$cliente[contraseña]\"";
        $condicion = "where email = \"$_SESSION[cliente]\"";

        // Actualizamos el perfil en la base de datos
        $crud->actualizar("clientes", $valores, $condicion);

        // Ventana que indica que el perfil se ha actualizado correctamente
        echo "<dialog open>
              <p>El perfil se ha actualizado correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
        </dialog>";
    }
?>

<?php
    // Cargamos la cabecera
    require_once "../vista/template/header.php";
?>

    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once "../vista/template/nav.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col d-flex align-items-center">
                <?php
                    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
                ?>
                
                <div class="col-md-5 border-right">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Perfil</h4>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="labels">Nombre</label>
                                    <input type="text" class="form-control" placeholder="Nombre" name="Nombre" value="<?php echo $cliente['nombre'] ?>">
                                    <div class="invalid-feedback">
                                        Introduzca un nombre
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label class="labels">Contraseña</label>
                                    <input type="text" class="form-control" placeholder="Contraseña" name="Contraseña" value="">
                                    <div class="invalid-feedback">
                                        Introduzca una contraseña válida
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="labels">Confirmar contraseña</label>
                                    <input type="text" class="form-control" placeholder="Contraseña" name="Confirmar contraseña" value="">
                                    <div class="invalid-feedback">
                                        Confirme la contraseña
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="labels">Teléfono</label>
                                    <input type="tel" class="form-control" placeholder="Teléfono" name="Telefono" value="<?php echo $cliente['telefono'] ?>">
                                    <div class="invalid-feedback">
                                        Introduzca un número de teléfono válido
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar perfil</button></div>
                        </div>
                    </div>
                    <?php
                        // Si hay algún error lo mostramos aquí
                        if (isset($_SESSION['error'])) {
                            echo "<div class='mt-3 text-danger font-weight-bold text-lg'>";
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            echo "</div>";
                        }
                    ?>
                </form>
            </div>
        </div>
    </div>
        <?php
            // Cargamos el pie
            require_once "../vista/template/footer.php";
        ?>
    </body>
</html>