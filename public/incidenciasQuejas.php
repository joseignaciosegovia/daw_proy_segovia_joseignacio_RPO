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

    // Si pulsamos el botón de "Enviar"
    if (isset($_POST['datos'])) {

        $datos = json_decode($_POST['datos']);

        $fecha = date('Y-m-d', time());

        // Añadimos la queja/sugerencia al perfil del usuario en la base de datos
        $crud->insertar("sugerencias_incidencias", "\"$fecha\", \"$datos->contenido\", \"$_SESSION[cliente]\"");

        // Ventana que indica que el perfil se ha actualizado correctamente
        echo "<dialog open>
              <p>La queja o sugerencia se ha realizado correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
        </dialog>";
    }

    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/navCliente.php";
?>
            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-md-6 d-flex align-items-center">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="enviarIncidencias">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Quejas y sugerencias</h4>
                        </div>
                        <div>
                            <div>
                                <label class="labels">Queja o sugerencia</label>
                                <textarea class="form-control" id="quejaIncidencia" placeholder="" name="Queja" value="" rows="5" cols="100" required></textarea>
                                <div class="invalid-feedback">
                                    Introduzca un mensaje
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Enviar">Realizar queja/sugerencia</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/footer.php";
    ?>
    </body>
</html>