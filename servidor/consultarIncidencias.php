<?php
    //ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si hemos iniciado sesión como gestor
    if(!empty($_SESSION["gestor"])){

        $crud = new Crud(new DB("proyecto"));
        $incidencias = $crud->listar("*", "sugerencias_incidencias", " order by fecha");

        if($incidencias == null) {
?>
            <h1 class="d-flex justify-content-center">No hay incidencias enviadas por usuarios</h1>
            <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
            <div class="container-fluid">
                <div class="row">
                    <!-- La barra de navegación será la primera columna -->
<?php 
                    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; 
        }
        else {

?>
    <h1 class="d-flex justify-content-center">Incidencias de los usuarios</h1>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

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
                            // Recorremos y mostramos las incidencias
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
 <?php   
        } 
    }
?>
    <a href="intranet.php"><button>Volver atrás</button></a>
</body>
</html>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>