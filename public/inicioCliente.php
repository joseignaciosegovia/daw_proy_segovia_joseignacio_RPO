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
        <link rel="stylesheet" type="text/css" href="/css/estilosInicioCliente.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
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

    $crud = new Crud(new DB("proyecto"));
    // Guardamos el cliente para que puedan mostrarse sus datos en la barra de navegación
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    // Datos que vamos a mostrar
    $reservasMes = $crud->listar("*", "reservas", "where cliente = \"$cliente[email]\" and MONTH(reservas.fecha) = MONTH(CURDATE()) AND YEAR(reservas.fecha) = YEAR(CURDATE()) order by fecha");
    $siguienteReserva = $crud->obtener("reservas", "where cliente = \"$cliente[email]\" and TIMESTAMP(fecha, horaInicio) > CONVERT_TZ(NOW(), @@session.time_zone, 'Europe/Madrid') ORDER BY TIMESTAMP(fecha, horaInicio) ASC LIMIT 1");
    // Si hay reservas para fechas futuras
    if($siguienteReserva != null) {
        $siguienteReserva = $siguienteReserva[0];
        $pistaSiguienteReserva = $crud->obtener("pistas", "where id = $siguienteReserva[pista]")[0];
    }
    $numeroSugerencias = $crud->listar("count(*)", "sugerencias_incidencias, clientes", "where sugerencias_incidencias.cliente = \"$cliente[email]\"");
    if($numeroSugerencias != null)
        $numeroSugerencias = $numeroSugerencias[0]['count(*)'];
    $numeroPistas = $crud->listar("count(*)", "pistas", "")[0]['count(*)'];
    $numeroInstalaciones = sizeof($crud->listar("localizacion, count(*)", "pistas", "group by localizacion"));
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
            <!-- BIENVENIDA -->
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
            <!-- SECCIÓN: RESERVAS -->
            <div class="card shadow-sm border-0">
                <div class="p-3 py-4">
                    <div>
                        <div class="section-header">
                            <span><i class="ti ti-calendar" aria-hidden="true"></i> Reservas</span>
                            <a href="/public/reservasCliente.php">Ver todas <i class="ti ti-arrow-right" aria-hidden="true"></i></a>
                        </div>
                        <div class="dash-grid-3">
                            <!-- Tarjeta: reservas este mes -->
                            <div class="dash-card dash-card-accent">
                                <div class="lbl"><i class="ti ti-calendar-stats" aria-hidden="true"></i> Reservas este mes</div>
                                <?php if($reservasMes != null) { ?>
                                    <div class="val"><?php echo sizeof($reservasMes); ?></div>
                                <?php }
                                else { ?>
                                    <div class="val">0</div>
                                <?php } ?>
                                <div class="card-sub"><span class="badge badge-green">Mayo 2026</span></div>
                            </div>
                            <!-- Tarjeta: próxima reserva (ocupa 2 columnas) -->
                            <div class="dash-card" style="grid-column: span 2;">
                                <!-- Si hay reservas futuras -->
                                <?php if($siguienteReserva != null) { ?>
                                <div class="lbl"><i class="ti ti-clock" aria-hidden="true"></i> Próxima reserva</div>
                                <div class="next-card">
                                    <div class="next-icon">
                                        <i class="ti ti-soccer-field" aria-hidden="true"></i>
                                    </div>
                                    <div class="next-info">
                                        <div class="val-md"><?php echo "$pistaSiguienteReserva[nombre]"; ?></div>
                                        <div class="card-sub">
                                            <i class="ti ti-calendar-event" aria-hidden="true"></i>
                                            <?php echo "$siguienteReserva[fecha]"; ?>
                                        </div>
                                        <div class="pill-row">
                                            <span class="badge badge-blue"><i class="ti ti-clock" aria-hidden="true"></i> <?php echo "$siguienteReserva[horaInicio]"; ?></span>
                                            <span class="badge badge-amber"><i class="ti ti-map-pin" aria-hidden="true"></i> <?php echo "$pistaSiguienteReserva[localizacion]"; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php } 
                                else { ?>
                                <!-- Si no hay reservas futuras -->
                                <div class="lbl"><i class="ti ti-clock" aria-hidden="true"></i> No tiene reservas en fechas futuras</div>
                                <div class="next-card">
                                    <div class="next-icon">
                                        <i class="ti ti-ball-football" aria-hidden="true"></i>
                                    </div>
                                    <div class="next-info">
                                        <div class="val-md"></div>
                                        <div class="card-sub">
                                            <i class="ti ti-calendar-event" aria-hidden="true"></i>
                                            
                                        </div>
                                        <div class="pill-row">
                                            <span class="badge badge-blue"><i class="ti ti-clock" aria-hidden="true"></i> </span>
                                            <span class="badge badge-amber"><i class="ti ti-map-pin" aria-hidden="true"></i> </span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0 mb-4" style="border-color: #dee2e6;">
                    <!-- SECCIÓN: INCIDENCIAS -->
                    <div>
                        <div class="section-header">
                            <span><i class="ti ti-mail" aria-hidden="true"></i> Incidencias</span>
                            <a href="/public/incidenciasQuejas.php">Ver todas <i class="ti ti-arrow-right" aria-hidden="true"></i></a>
                        </div>
                        <div class="dash-grid-2">
                            <div class="dash-card dash-card-accent dash-card-accent-amber">
                                <div class="lbl"><i class="ti ti-send" aria-hidden="true"></i> Incidencias enviadas</div>
                                <div class="val"><?php echo "$numeroSugerencias"; ?></div>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0 mb-4" style="border-color: #dee2e6;">
                    <!-- SECCIÓN: INSTALACIONES -->
                    <div>
                        <div class="section-header">
                            <span><i class="ti ti-soccer-field" aria-hidden="true"></i> Pistas e instalaciones</span>
                            <a href="/public/reservarPista.php">Reservar pista <i class="ti ti-arrow-right" aria-hidden="true"></i></a>
                        </div>
                        <div class="dash-grid-3">
                            <!-- Tarjeta: pistas disponibles -->
                            <div class="dash-card dash-card-accent dash-card-accent-blue">
                                <div class="lbl"><i class="ti ti-stack" aria-hidden="true"></i> Pistas disponibles</div>
                                <div class="val"><?php echo "$numeroPistas"; ?></div>
                            </div>
                            <!-- Tarjeta: datos de instalaciones (ocupa 2 columnas) -->
                            <div class="dash-card" style="grid-column: span 2;">
                                <div class="info-row">
                                    <span class="info-lbl"><i class="ti ti-building" aria-hidden="true"></i> Instalaciones</span>
                                    <span class="info-val"><?php echo "$numeroInstalaciones"; ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-lbl"><i class="ti ti-door-enter" aria-hidden="true"></i> Hora de apertura</span>
                                    <span class="info-val">08:00</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-lbl"><i class="ti ti-door-exit" aria-hidden="true"></i> Hora de cierre</span>
                                    <span class="info-val">22:00</span>
                                </div>
                            </div>
                        </div>
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