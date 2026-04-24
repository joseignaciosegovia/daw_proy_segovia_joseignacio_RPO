<?php
    ob_start(); // activa el buffer
    session_start();

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de crear pista
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $localizacion = $datos->localizacion[0];
        $valores = "\"$datos->nombre\", \"$localizacion\", \"$datos->precio\"";

        // Añadimos la pista en la base de datos
        $crud->insertarColumnas("pistas", "(nombre, localizacion, precioReserva)", $valores);
        header("Location: intranet.php");
    }

    if(isset($_POST['Añadir'])) {

?>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col-12 col-lg-8 d-flex align-items-center">
                    <form method="POST" name="añadirPista" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Crear pista</h4>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="labels">Nombre</label>
                                    <input type="text" id="nombre" class="form-control" name="Nombre" value="" required>
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
                                    <label for="Localizacion">Localización</label>
                                    <select name="Localizacion" id="Localizacion">
                                        <?php
                                            $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                                            // Añadimos las localizaciones a las opciones del select
                                            foreach($localizaciones as $localizacion){
                                                echo "<option value=\"$localizacion[localizacion]\">$localizacion[localizacion]</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="labels">Precio de Reserva</label>
                                    <input type="number" class="form-control" id="precio" name="Precio" value="" required>
                                    <div class="invalid-feedback">
                                        Introduzca un precio válido
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Crear">Crear pista</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <a href="intranet.php"><button>Volver atrás</button></a>
    </div>
<?php
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>