<?php
    // ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

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
        <script type="module" src="/js/confirmacion.js"></script>
<?php }

    // Si pulsamos el botón de actualizar
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $valores = "nombre = \"$datos->nombre\", localizacion = \"$datos->localizacion\", precioReserva = '$datos->precio'";        
        $condicion = "where id = $datos->id";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("pistas", $valores, $condicion);

        $_GET['pista'] = $datos->nombre;
    }

    // Si pulsamos el botón de borrar
    elseif (isset($_GET['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));

        $id = $crud->listar("id", "pistas", "where nombre = \"$_GET[Borrar]\"")[0]['id'];
        $crud->eliminar("pistas", "where id = $id");
        // En confirmacion.js está el mensaje para confirmar el borrado
        header("Location: intranet.php");
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Editar" de intranet.php)
    if(isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));
        $pista = $crud->obtener("pistas", "where id = $_GET[pista]")[0];
    
?>
    <h1 class="d-flex justify-content-center">Editar pista</h1>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8 d-flex align-items-center">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?pista=" . $pista['id']; ?>" name="editarPista">
                    <div class="p-3 py-5">
                        <div class="row mt-3">
                            <div class="col-md-12 mt-3">
                                <label class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $pista['nombre'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un nombre válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="Localizacion">Localización</label>
                                <select name="Localizacion" id="Localizacion">
                                    <?php
                                        // Obtenemos y recorremos las localizaciones
                                        $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                                        foreach($localizaciones as $localizacion){
                                            // Añadimos cada localización al select
                                            echo "<option value=\"$localizacion[localizacion]\"";
                                            // La opción indicada por defecto será la localización de la pista
                                            if($pista['localizacion'] == $localizacion['localizacion']){
                                                echo " selected";
                                            }
                                            
                                            echo ">$localizacion[localizacion]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?php echo $pista['precioReserva'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un precio válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar pista</button>
                            <button class="btn btn-danger profile-button" name="Borrar" id="borrar">Borrar pista</button>
                        </div>
                        <!-- Campo oculto para guardar el id de la pista para poder actualizarlo -->
                        <input id="id" type="hidden" value="<?php echo "$pista[id]"?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <button class="btn btn-primary form-floating" onclick="window.location.href='intranet.php';">Volver atrás</button>

<?php
    }
    // Si no hemos llegado a esta página a través del botón "Editar" de intranet.php, volvemos a dicha página
    else {
        header('Location: intranet.php');
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>