<?php
    session_start();

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de los administradores
    if (empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));
    $pistas = $crud->listar("*", "pistas", "");

?>
    <h1 class="d-flex justify-content-center">Lista de Pistas</h1>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8">
                <table class="table table-striped table-hover">
                    <thead>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Localización</th>
                        <th>Reservas</th>
                        <th>Calendario</th>
                        <th>Editar pista</th>
                    </thead>
                    <tbody>
                        <?php
                            $cont = 1;
                            // Recorremos las pistas y las añadimos a la tabla
                            foreach($pistas as $pista){
                        ?>
                        <tr>
                            <th><?php echo $cont ?></th>
                            <td><?php echo $pista['nombre'] ?></td>
                            <td><?php echo $pista['localizacion'] ?></td>
                            <td><?php echo "<a href=\"consultarReservas.php?pista=$pista[nombre]\"><button>Consultar reservas</button></a>"?></td>
                            <td><?php echo "<a href=\"calendarioPista.php?pista=$pista[nombre]\"><button>Calendario</button></a>"?></td>
                            <td><?php echo "<a href=\"editarPista.php?pista=$pista[nombre]\"><button>Editar</button></a>"?></td>
                        </tr>
                            <?php 
                                $cont++;
                            } 
                            ?>
                        <tr>
                            <td colspan="2">
                                <form method='POST' action='<?php echo "añadirPista.php"; ?>'>
                                    <input type="submit" class="btn btn-primary" name="Añadir" value="Añadir pista">
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>