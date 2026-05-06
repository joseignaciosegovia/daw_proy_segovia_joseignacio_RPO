<?php
    // ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
        unset($_SESSION['gestor']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"]) && empty($_SESSION["administrador"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Actualizamos el título de la página
    $titulo = "Administración de gestores | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si hemos iniciado sesión como gestor, pero éste no es administrador, volvemos a la página de gestión de pistas
    if (!empty($_SESSION["gestor"]) && empty($_SESSION["administrador"])) {
        header("Location: intranet.php");
        exit();
    }

    // Si hemos iniciado sesión como gestor y el gestor es administrador
    if(!empty($_SESSION["gestor"]) && !empty($_SESSION["administrador"])){
        $crud = new Crud(new DB("proyecto"));
        $pistas = $crud->listar("*", "pistas", "");
        $nombre = $crud->listar("nombre", "gestores", "where email = \"$_SESSION[gestor]\"")[0]['nombre'];
        $gestores = $crud->listar("*", "gestores", "");
?>

<h1 class="d-flex justify-content-center">Lista de Gestores</h1>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8"></div>
            <table class="table table-striped table-hover">
                <thead>
                    <th>#</th>
                    <th>Correo</th>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Teléfono</th>
                    <th>¿Es administrador?</th>
                    <th>Editar gestor</th>
                </thead>
                <tbody>
                    <?php
                        $cont = 1;
                        // Recorremos los gestores y los añadimos a la tabla
                        foreach($gestores as $gestor){
                    ?>
                    <tr>
                        <th><?php echo $cont ?></th>
                        <td><?php echo $gestor['email'] ?></td>
                        <td><?php echo $gestor['nombre'] ?></td>
                        <td><?php echo $gestor['DNI'] ?></td>
                        <td><?php echo $gestor['telefono'] ?></td>
                        <td><?php 
                            if($gestor['administrador'] == 1)
                                echo "Sí";
                            else
                                echo "No";
                        ?>
                        </td>
                        <td><?php echo "<a href=\"editarGestor.php?gestor=$gestor[email]\"><button>Editar</button></a>"?></td>
                    </tr>
                        <?php 
                            $cont++;
                        } 
                        ?>
                    <tr>
                        <td colspan="2">
                            <form method='POST' action='<?php echo "añadirGestor.php"; ?>'>
                                <input type="submit" class="btn btn-primary" name="Añadir" value="Añadir gestor">
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
    <?php } ?>
    <a href="intranet.php"><button>Volver atrás</button></a>
    </body>
</html>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>