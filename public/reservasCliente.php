<?php
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";

    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: perfilCliente.php');
        die();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página para iniciar sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    $crud = new Crud(new DB("proyecto"));

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Cargamos la cabecera
    require_once "../vista/template/header.php";

    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
?>

    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once "../vista/template/nav.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col d-flex align-items-center">
                <h4>Lista de reservas</h4>
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
                                    <th scope="col">Cancelar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($reservas as $reserva){
                                        echo "<tr>";
                                            echo "<td>$reserva[fecha]</td>";
                                            echo "<td>$reserva[hora]</td>";
                                            echo "<td>$reserva[pista]</td>";
                                            echo "<form method=\"post\" action=\"../servidor/actualizarCalendario.php\">";
                                            echo "<input name=\"fecha\" type=\"hidden\" value=\"$reserva[fecha]\">";
                                            echo "<input name=\"hora\" type=\"hidden\" value=\"$reserva[hora]\">";
                                            echo "<input name=\"pista\" type=\"hidden\" value=\"$reserva[pista]\">";
                                            echo "<td><input type=\"submit\" value=\"Borrar\" name=\"Cancelar\"></td>";
                                            echo "</form>";
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