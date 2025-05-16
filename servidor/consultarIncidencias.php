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
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/proyecto/imagenes/Moral.png"/>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
        <!-- Animanate CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    </head>
    <body>
<?php

    $crud = new Crud(new DB("proyecto"));

    $incidencias = $crud->listar("*", "sugerencias_incidencias", "");

?>

    <table class="table table-hover">
            <thead>
                <th>#</th>
                <th>Fecha</th>
                <th>Contenido</th>
                <th>Usuario</th>
            </thead>
            <tbody>
<?php
                $cont = 1;
                foreach($incidencias as $incidencia){
                    ?>
                <tr>
                    <th><?php echo $cont ?></th>
                    <td><?php echo $incidencia['fecha'] ?></td>
                    <td><?php echo $incidencia['contenido'] ?></td>
                    <td><?php echo $incidencia['cliente'] ?></td>
                </tr>
                <?php 
                    $cont++;
                } 
                ?>
        </tbody>
    </table>
    <form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <input type="submit" class="btn-salir" name="salir" value="Cerrar sesión">
    </form>
</body>
</html>
<?php
    // Mensaje de error cuando volvemos después de pinchar en algún botón (como cuando no hay reservas para la pista seleccionada)
    if (isset($_SESSION['error'])) {
        echo "<div class='mt-3 text-danger font-weight-bold text-lg'>";
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        echo "</div>";
    }
?>