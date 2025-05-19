<?php

    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/modelo/Cliente.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página principal
    if(isset($_POST['salir'])) {
        unset($_SESSION['cliente']);
        header("Location: ../index.php");
    }

    $crud = new Crud(new DB("proyecto"));
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];

    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/nav.php";
?>
        <div class="col">
            <h4>Escoger pista</h4>
            <div class="accordion accordion-flush" id="elegirPista">
                <?php
                    $contador = 0;
                    $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                    foreach($localizaciones as $localizacion){
                ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo "#flush-collapse$contador"; ?>" aria-expanded="false" aria-controls="<?php echo "flush-collapse$contador"; ?>">
                                    <?php echo "$localizacion[localizacion]"; ?>
                                </button>
                            </h2>
                            <div id="<?php echo "flush-collapse$contador"; ?>" class="accordion-collapse collapse" data-bs-parent="#elegirPista">
                                <?php
                                    $pistas = $crud->listar("nombre", "pistas", "where localizacion = \"$localizacion[localizacion]\"");
                                    foreach($pistas as $pista){
                                ?>
                                        <div class="accordion-body">
                                            <a class="nav-link ms-3 my-1"><?php echo "$pista[nombre]"; ?></a>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                <?php
                        $contador++;
                    }
                ?>
                        </div>
                </div>
            </div>    
        </div>
    <!-- Cerramos la sección principal, creada en nav.php -->
    </div>
    
    <div class="col" id="calendario">

        <!-- CUANDO SE HAGA UNA RESERVA, HAY QUE ACTUALIZAR TAMBIÉN LA TABLA CALENDARIOS -->
    </div>

    <!-- Botón de cerrar sesión -->
    <form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <input type="submit" class="btn-salir" name="salir" value="Cerrar sesión">
    </form>

<?php 
    require_once "../vista/template/footer.php";
?>
