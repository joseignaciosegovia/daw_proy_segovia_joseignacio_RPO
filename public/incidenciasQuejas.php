<?php
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <script type="module" src="/js/validacion.js"></script>
<?php }

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de "Enviar"
    if (isset($_POST['datos'])) {

        $datos = json_decode($_POST['datos']);
        $fecha = date('Y-m-d', time());

        // Añadimos la queja/sugerencia al perfil del usuario en la base de datos
        $crud->insertarColumnas("sugerencias_incidencias", "(fecha, contenido, cliente)", "\"$fecha\", \"$datos->contenido\", \"$_SESSION[cliente]\"");
    }

    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
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
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="Enviar">Realizar queja/sugerencia</button>
                        </div>
                    </div>
                </form>
            </div>

<?php
    $sugerencias = $crud->listar("fecha, contenido", "sugerencias_incidencias", "where cliente = \"$_SESSION[cliente]\"");

    if($sugerencias == null) {
        echo "<h4 class=\"d-flex justify-content-center py-2\">No has realizado ninguna sugerencia/incidencia</h4>";
    }

    else{

?>
        <h4 class="d-flex justify-content-center py-2">Historial de sugerencias/incidencias</h4>
        <div class="accordion accordion-flush">
            <table class="table table-hover">
                <?php $contador = 1; ?>
                <thead>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Contenido</th>
                </thead>
                <tbody>
                    <?php foreach($sugerencias as $sugerencia){ ?>
                    <tr>
                        <td><?php echo $contador ?></td>
                        <td><?php echo $sugerencia['fecha'] ?></td>
                        <td><?php echo $sugerencia['contenido'] ?></td>
                        <?php $contador++; ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    
    }
        // Cargamos el pie
        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
    ?>
    </body>
</html>