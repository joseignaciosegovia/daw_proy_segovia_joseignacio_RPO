<?php

    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/proyecto/js/calendarioCliente.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, volvemos a la página principal
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];

    echo "<h2 class=\"d-flex justify-content-center py-2\" id=\"bienvenido\">Bienvenido/a $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/navCliente.php";
?>
        <div class="col-8 col-sm-6">
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
        <!-- Cerramos la sección principal, creada en navCliente.php -->
        <h3 id="tituloPista" class="d-flex justify-content-center"></h3>
    </div>
    
    <div class="col" id="calendario">
        
    </div>
    <!-- Incluimos el email del cliente para que JavaScript pueda identificarle -->
    <p id="cliente" hidden><?php echo $_SESSION['cliente'] ?></p>

<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/footer.php";
?>
