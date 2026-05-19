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

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/js/calendarioCliente.js"></script>
<?php }

    // Devuelve las iniciales de una cadena con distintas palabras
    function iniciales(string $nombre): string {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

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
    // Datos que vamos a mostrar
    $fecha = new DateTime();
    // Formato de fecha en español
    $formatter = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE
    );
    $iniciales = iniciales($cliente['nombre']);
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
?>
        <main class="main">
            <div class="welcome-bar">
                <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
                <div class="welcome-text">
                    <h1>Bienvenida/o, <?php echo "$cliente[nombre]"; ?></h1>
                    <p>Hoy es <?php echo $formatter->format($fecha);?></p>
                </div>
                <span class="badge badge-green">
                    <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
                </span>
            </div>
            <div class="card shadow-sm border-0">
                <div class="p-3 py-4">
                    <div class="section-header mb-4">
                        <i class="ti ti-plus" aria-hidden="true"></i>
                        <div>
                            <h2>Reservar pista</h2>
                            <small class="text-muted">Escoge la pista en la que reservar un horario</small>
                        </div>
                    </div>
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
                                <input name="id" type="hidden" value=<?php echo "$pista[id]"; ?>>
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
            </div>  
            <!-- Div en el que irá el título de la pista -->
            <div class="d-flex flex-column align-items-center" id="tituloPista">

            </div>  
        </main>
        <!-- Cerramos la sección principal, creada en navCliente.php -->
    </div>
    <!-- Div en el que se mostrará el calendario de la pista seleccionada -->
    <div class="col" id="calendario">
        
    </div>
    <!-- Incluimos el email del cliente para que JavaScript pueda identificarle -->
    <p id="cliente" hidden><?php echo $_SESSION['cliente'] ?></p>

<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>