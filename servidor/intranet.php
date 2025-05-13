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
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
        <!-- Animanate CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    </head>
    <body>
<?php

    $crud = new Crud(new DB("proyecto"));

    $pistas = $crud->listar("*", "pistas", "");

?>
    <table class="table table-hover">
        <thead>
            <th>#</th>
            <th>Nombre</th>
            <th>Localización</th>
            <th>Precio de reserva</th>
            <th>Reservas</th>
            <th>Calendario</th>
            <th>Editar</th>
        </thead>
        <tbody>
            <?php
                $cont = 1;
                foreach($pistas as $pista){
            ?>
            <tr>
                <th><?php echo $cont ?></th>
                <td><?php echo $pista['nombre'] ?></td>
                <td><?php echo $pista['localizacion'] ?></td>
                <td><?php echo $pista['precioReserva'] . "€" ?></td>
                <td><?php echo "<a href=\"consultarReservas.php?pista=$pista[nombre]\"><button>Consultar reservas</button></a>"?></td>
                <td><?php echo "<a href=\"consultarCalendario.php?pista=$pista[nombre]\"><button>Calendario</button></a>"?></td>
                <td><?php echo "<a href=\"editarPista.php?pista=$pista[nombre]\"><button>Editar</button></a>"?></td>
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