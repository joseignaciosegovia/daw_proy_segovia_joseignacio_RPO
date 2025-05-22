<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    function añadirScriptsPie(){
?>
        <script type="module" src="/proyecto/js/validacion.js"></script>
        <script type="module" src="/proyecto/js/confirmacion.js"></script>
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

    // Si pulsamos el botón de actualizar
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $valores = "nombre = \"$datos->nombre\", localizacion = \"$datos->localizacion\", precioReserva = '$datos->precio'";        
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
             <?php require_once "../vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col d-flex align-items-center">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?pista=" . $pista['nombre']; ?>" name="editarPista">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Pista</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="Nombre" value="<?php echo $pista['nombre'] ?>" required>
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
                                            echo "<option value=\"$localizacion[localizacion]\"";

                                            if($pista['localizacion'] == $localizacion['localizacion']){
                                                echo " selected";
                                            }
                                            
                                            echo ">$localizacion[localizacion]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                
                            <?php
                                $precios = json_decode($pista['precioReserva']);
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
                                else {
                            ?>
                                <label class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" id="precioUnico" name="Precio" value="<?php echo $precios->precioUnico ?>">
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
                            <!-- <a href="editarPista.php?Borrar=<?php echo "$pista[nombre]"?>"><button class="btn btn-danger profile-button" name="Borrar" id="borrar">Borrar pista</button></a> -->
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

    // Mensaje de error cuando volvemos después de pinchar en algún botón (como cuando no hay reservas para la pista seleccionada)
    if (isset($_SESSION['error'])) {
        echo "<div class='mt-3 text-danger font-weight-bold text-lg'>";
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        echo "</div>";
    }

    require_once "../vista/template/footer.php";
?>