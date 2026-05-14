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

    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
?>
        <main class="main">
            <div>
                <h1 class="d-flex justify-content-center py-2" id="bienvenido">Bienvenido/a <?php echo "$cliente[nombre]"; ?></h1>
                <div class="section-title">Información sobre las reservas: <a href="/public/reservasCliente.php">Ver todas las reservas →</a></div>
                <div class="cards-grid">
                    <div class="stat-card accent">
                        <div class="label">Reservas este mes</div>
                        <div class="value">3</div>
                        <div class="sub">Última: Pádel 1 · lun 12</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Próxima reserva</div>
                        <div class="value" style="font-size:15px;margin-top:2px;">Mañana</div>
                        <div class="sub">Pádel 2 · 18:00 h</div>
                    </div>
                    
                </div>
                <div class="section-title">Información sobre las incidencias: <a href="/public/incidenciasQuejas.php">Ver todas las incidencias enviadas →</a></div>
                <div class="cards-grid">
                    <div class="stat-card accent">
                        <div class="label">Incidencias enviadas</div>
                        <div class="value">
                            <?php 
                                $numeroSugerencias = $crud->listar("count(*)", "sugerencias_incidencias, clientes", "where sugerencias_incidencias.cliente = clientes.email")[0]['count(*)'];
                            echo "$numeroSugerencias";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="section-title">Información sobre las pistas: <a href="/public/reservarPista.php">Reservar pista →</a></div>
<?php
                    $crud = new Crud(new DB("proyecto"));
                    $numeroPistas = $crud->listar("count(*)", "pistas", "")[0]['count(*)'];
                    $numeroInstalaciones = sizeof($crud->listar("localizacion, count(*)", "pistas", "group by localizacion"));
?>
                <div class="cards-grid">
                    <div class="stat-card accent">
                        <div class="label">Pistas disponibles</div>
                        <div class="value"><?php echo "$numeroPistas"; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Instalaciones</div>
                        <div class="value" style="font-size:15px;margin-top:2px;"><?php echo "$numeroInstalaciones"; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Hora de apertura</div>
                        <div class="value" style="font-size:15px;margin-top:2px;">08:00</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Hora de cierre</div>
                        <div class="value" style="font-size:15px;margin-top:2px;">22:00</div>
                    </div>
                </div>
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