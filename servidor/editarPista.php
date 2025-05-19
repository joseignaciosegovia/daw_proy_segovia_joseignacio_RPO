<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

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

    // Si pulsamos el botón de actualizar
    if (isset($_POST['Actualizar'])) {
        $valores = "nombre = \"$_POST[Nombre]\", localizacion = \"$_POST[Localizacion]\", precioReserva = \"$_POST[Precio]\"";
        $condicion = "where nombre = \"$_GET[pista]\"";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("pistas", $valores, $condicion);

        // Ventana que indica que la pista se ha actualizado correctamente
        echo "<dialog open>
            <p>La pista se ha actualizado correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
        </dialog>";

        $_GET['pista'] = $_POST['Nombre'];
    }

    // Si pulsamos el botón de borrar
    elseif (isset($_POST['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("pistas", "where nombre = \"$_GET[pista]\"");

        // Ventana que indica que la pista se ha borrado correctamente
        echo "<dialog open>
            <p>La pista se ha borrado correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
        </dialog>";

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
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?pista=" . $pista['nombre']; ?>">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Pista</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">Nombre</label>
                                <input type="text" class="form-control" name="Nombre" value="<?php echo $pista['nombre'] ?>">
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
                                <label class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" name="Precio" value="<?php echo $pista['precioReserva'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un precio válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar pista</button></div>
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Borrar">Borrar pista</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="intranet.php"><button>Volver atrás</button></a>
    </body>
</html>
<?php
    }

    // Mensaje de error cuando volvemos después de pinchar en algún botón (como cuando no hay reservas para la pista seleccionada)
    if (isset($_SESSION['error'])) {
        echo "<div class='mt-3 text-danger font-weight-bold text-lg'>";
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        echo "</div>";
    }
?>