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
    $pistas = $crud->listar("*", "pistas", "");

?>
    <h1 class="d-flex justify-content-center">Lista de Pistas</h1>
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
                        <th>Nombre</th>
                        <th>Localización</th>
                        <th>Precio de reserva</th>
                        <th>Reservas</th>
                        <th>Calendario</th>
                        <th>Editar pista</th>
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
                
            </div>
        </div>
    </div>
    <form method='POST' action='<?php echo "añadirPista.php"; ?>'>
        <input type="submit" class="btn btn-primary" name="Añadir" value="Añadir pista">
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