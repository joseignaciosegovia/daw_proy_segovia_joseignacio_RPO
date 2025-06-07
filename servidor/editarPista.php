<?php
    session_start();

    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/proyecto/js/validacion.js"></script>
        <script type="module" src="/proyecto/js/confirmacion.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Si pulsamos el botón de actualizar
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $valores = "localizacion = \"$datos->localizacion\", precioReserva = '$datos->precio'";        
        $condicion = "where nombre = \"$datos->nombreOriginal\"";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("pistas", $valores, $condicion);

        $_GET['pista'] = $datos->nombre;
    }

    // Si pulsamos el botón de borrar
    elseif (isset($_GET['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("pistas", "where nombre = \"$_GET[Borrar]\"");

        header("Location: intranet.php");
    }

    if(isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        $pista = $crud->obtener("pistas", "where nombre = \"$_GET[pista]\"")[0];
    
?>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8 d-flex align-items-center">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?pista=" . $pista['nombre']; ?>" name="editarPista">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Pista: <?php echo "$_GET[pista]" ?></h4>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="Localizacion">Localización</label>
                                <select name="Localizacion" id="Localizacion">
                                    <?php
                                        $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                                        foreach($localizaciones as $localizacion){
                                            echo "<option value=\"$localizacion[localizacion]\"";

                                            if($pista['localizacion'] == $localizacion['localizacion']){
                                                echo " selected";
                                            }
                                            
                                            echo ">$localizacion[localizacion]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                
                            <?php
                                $precios = json_decode($pista['precioReserva']);
                                // Si la localización de la pista es "Ciudad Deportiva", mostramos los distintos precios
                                if($pista['localizacion'] == "Ciudad Deportiva"){
                            ?>
                                <label class="labels">Precios de Reserva</label>
                                <table class="table table-hover">
                                    <thead>
                                        <th colspan="2">Adultos</th>
                                        <th colspan="2">Menores de edad</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Normal</td>
                                            <td>Con luz</td>
                                            <td>Normal</td>
                                            <td>Con luz</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="number" class="form-control" id="adultoNormal" name="Precio" value="<?php echo $precios->adultoNormal?>">
                                                <div class="invalid-feedback">
                                                    Introduzca un precio válido
                                                </div>
                                                <div class="valid-feedback">
                                                    Dato correcto
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="adultoConLuz" name="Precio" value="<?php echo $precios->adultoConLuz?>">
                                                <div class="invalid-feedback">
                                                    Introduzca un precio válido
                                                </div>
                                                <div class="valid-feedback">
                                                    Dato correcto
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="menorNormal" name="Precio" value="<?php echo $precios->menorNormal?>">
                                                <div class="invalid-feedback">
                                                    Introduzca un precio válido
                                                </div>
                                                <div class="valid-feedback">
                                                    Dato correcto
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="menorConLuz" name="Precio" value="<?php echo $precios->menorConLuz?>">
                                                <div class="invalid-feedback">
                                                    Introduzca un precio válido
                                                </div>
                                                <div class="valid-feedback">
                                                    Dato correcto
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php
                                }
                                // Si la localización de la pista no es "Ciudad Deportiva", mostramos solo un precio
                                else {
                            ?>
                                <label class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" id="precioUnico" name="Precio" value="<?php echo $precios ?>">
                                <div class="invalid-feedback">
                                    Introduzca un precio válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar pista</button>
                            <button class="btn btn-danger profile-button" name="Borrar" id="borrar">Borrar pista</button>
                        </div>
                        <!-- Campo oculto para guardar el nombre de la pista antes de actualizarlo -->
                        <input id="nombreOriginal" type="hidden" value="<?php echo "$pista[nombre]"?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="intranet.php"><button>Volver atrás</button></a>

<?php
    }

    else {
        header('Location: intranet.php');
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/footer.php";
?>