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

    // Si pulsamos el botón de actualizar
    if (isset($_POST['Actualizar'])) {
        $valores = "nombre = \"$_POST[Nombre]\", localizacion = \"$_POST[Localizacion]\", precioReserva = \"$_POST[Precio]\"";
        $condicion = "where nombre = \"$_GET[pista]\"";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("pistas", $valores, $condicion);

        // Ventana que indica que el perfil se ha actualizado correctamente
        echo "<dialog open>
            <p>La pista se ha actualizado correctamente</p>
            <button onclick=\"this.parentElement.close()\">OK</button>
        </dialog>";

        $_GET['pista'] = $_POST['Nombre'];
    }

    if(isset($_GET['pista'])) {

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gestión de las pistas</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
        <!-- Animanate CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    </head>
    <body>
<?php

    $crud = new Crud(new DB("proyecto"));

    $pista = $crud->obtener("pistas", "where nombre = \"$_GET[pista]\"")[0];
    
?>
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
            <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar perfil</button></div>
        </div>
    </form>
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