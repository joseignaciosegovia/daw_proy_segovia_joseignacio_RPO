<?php
    //ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/js/calendarioCliente.js"></script>
<?php }

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
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
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
?>
        <div class="col-8 col-sm-6">
            <h4>Escoger pista</h4>
            <div class="accordion accordion-flush" id="elegirPista">
            <?php
                $contador = 0;
                // Obtenemos todas las localizaciones y las añadimos al acordeón
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
                        // Para cada localización, añadimos las pistas al acordeón
                        $pistas = $crud->listar("nombre, id", "pistas", "where localizacion = \"$localizacion[localizacion]\"");
                        foreach($pistas as $pista){
                    ?>
                            <div class="accordion-body">
                                <input id="id" name="id" type="hidden" value=<?php echo "$pista[id]"; ?> />
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
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>