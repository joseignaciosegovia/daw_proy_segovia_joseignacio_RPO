<?php
    session_start();

    require_once "../controlador/Crud.php";
    use Clases\DB;

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: intranet.php');
        die();
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

    // Si se obtiene la variable "pista" (pulsando el botón "Consultar reservas" de accesoAdministrador.php)
    if (isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));

        $reservas = $crud->listar("*", "reservas", "where pista = \"$_GET[pista]\"");
        if($reservas == null){
            // NO IMPRIME EL MENSAJE, SINO QUE DIRECTAMENTE VA A intranet.php
            error("No hay ninguna reserva para la pista " . $_GET['pista']);
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
        <h1>Reservas de la pista <?php echo "$_GET[pista]" ?></h1>
        <table class="table table-hover">
            <thead>
                <th>#</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Cliente</th>
            </thead>
            <tbody>
        <?php
            $cont = 1;
            
            foreach($reservas as $reserva){
        ?>
            <tr>
                <th><?php echo $cont ?></th>
                <td><?php echo $reserva['fecha'] ?></td>
                <td><?php echo $reserva['hora'] ?></td>
                <td><?php echo $reserva['cliente'] ?></td>
            </tr>
        <?php 
                $cont++;
            }
        ?>
            </tbody>
        </table>
        <a href="intranet.php"><button>Volver atrás</button></a>
    </body>
</html>

<?php
    }
?>