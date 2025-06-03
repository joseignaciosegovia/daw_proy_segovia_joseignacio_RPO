<?php
    session_start();

    $titulo = "Gestión de pistas | Moral de Calatrava";
    $home = "/proyecto/servidor/intranet.php";

    require_once "../controlador/Crud.php";
    require_once "../vista/template/header.php";
    use Clases\DB;

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));

    $incidencias = $crud->listar("*", "sugerencias_incidencias", "");

?>
    <h1 class="d-flex justify-content-center">Incidencias de los usuarios</h1>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once "../vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8 d-flex align-items-center">
                <table class="table table-striped table-hover">
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
            </div>
        </div>
    </div>
    <a href="intranet.php"><button>Volver atrás</button></a>
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

    require_once "../vista/template/footer.php";
?>