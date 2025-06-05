<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";
    use Clases\DB;

    function añadirScriptsPie(){
?>
        <script type="module" src="/proyecto/js/validacion.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_POST['salir'])) {
        unset($_SESSION['administrador']);
        header("Location: accesoAdministrador.php");
    }

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de crear
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $localizacion = $datos->localizacion[0];

        $valores = "\"$datos->nombre\", \"$localizacion\", \"$datos->precio\"";

        // Añadimos la pista en la base de datos
        
        $crud->insertar("pistas", $valores);
        header("Location: intranet.php");
    }

    if(isset($_POST['Añadir'])) {

?>

        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/navGestor.php"; ?>

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
                                            foreach($localizaciones as $localizacion){
                                                echo "<option value=\"$localizacion[localizacion]\">$localizacion[localizacion]</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
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
<?php
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/footer.php";
?>