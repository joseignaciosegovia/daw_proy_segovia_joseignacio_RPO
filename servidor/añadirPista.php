<?php
    session_start();

    require_once "../controlador/Crud.php";
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

    require_once "../vista/template/header.php";

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de crear
    if (isset($_POST['Crear'])) {
        $valores = "\"$_POST[Nombre]\", \"$_POST[Localizacion]\", \"$_POST[Precio]\"";

        // Creamos la pista en la base de datos
        
        $crud->insertar("pistas", $valores);

        // Ventana que indica que la pista se ha añadido correctamente
        echo "<dialog open>
            <p>La pista se ha añadido correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
        </dialog>";

        header("Location: intranet.php");
    }


    if(isset($_POST['Añadir'])) {

?>

        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once "../vista/template/navGestor.php"; ?>

                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col d-flex align-items-center">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Crear pista</h4>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="labels">Nombre</label>
                                    <input type="text" class="form-control" name="Nombre" value="">
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
                                    <input type="number" class="form-control" name="Precio" value="">
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