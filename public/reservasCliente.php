<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: perfilCliente.php');
        die();
    }

    $crud = new Crud(new DB("proyecto"));

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Cargamos la cabecera
    require_once "../vista/template/header.php";
?>

    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once "../vista/template/nav.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col d-flex align-items-center">
                <?php
                    $reservas = $crud->listar("*", "reservas", "where cliente = \"$_SESSION[cliente]\"");
                    if($reservas != null){
                        ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Hora</th>
                                    <th scope="col">Pista</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($reservas as $reserva){
                                        echo "<tr>";
                                            echo "<td>$reserva[fecha]</td>";
                                            echo "<td>$reserva[hora]</td>";
                                            echo "<td>$reserva[pista]</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>

    <?php
        // Cargamos el pie
        require_once "../vista/template/footer.php";
    ?>
    </body>
</html>