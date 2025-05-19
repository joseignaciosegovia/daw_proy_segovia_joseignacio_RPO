<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
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

        <h1>Reservas de la pista <?php echo "$_GET[pista]" ?></h1>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once "../vista/template/navGestor.php"; ?>

                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col d-flex align-items-center">
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
                </div>
            </div>
        </div>
        <a href="intranet.php"><button>Volver atrás</button></a>
    </body>
</html>

<?php
    }
?>