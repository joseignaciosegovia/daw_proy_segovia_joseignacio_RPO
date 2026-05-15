<!-- La barra de navegación será una columna dentro del contenido principal de la página -->
<div class="layout" id="seccionPrincipal">
    <nav class="sidebar" aria-label="Menú principal">
        <div class="user-chip">
            <div class="avatar"><img class="rounded-circle" src="<?php echo $cliente['foto'] ?>" alt="Foto de perfil" width="60" height="60" style="object-fit:cover;"></div>
            <div>
                <span><?php echo "$cliente[nombre]";  ?></span>
                <small>Usuario activo</small>
            </div>
        </div>
        <div class="nav-section">General</div>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/public/inicioCliente.php") echo "active"; ?>" href="/public/inicioCliente.php"><i class="ti ti-layout-dashboard" aria-hidden="true"></i> Inicio</a>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/public/perfilCliente.php") echo "active"; ?>" href="/public/perfilCliente.php"><i class="ti ti-user" aria-hidden="true"></i> Datos personales</a>
        <div class="nav-section">Reservas</div>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/public/reservarPista.php") echo "active"; ?>" href="/public/reservarPista.php"><i class="ti ti-plus" aria-hidden="true"></i> Nueva reserva</a>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/public/reservasCliente.php") echo "active"; ?>" href="/public/reservasCliente.php"><i class="ti ti-calendar" aria-hidden="true"></i> Mis reservas</a>
        <div class="nav-section">Soporte</div>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/public/incidenciasQuejas.php") echo "active"; ?>" href="/public/incidenciasQuejas.php"><i class="ti ti-mail" aria-hidden="true"></i> Buzón de incidencias</a>
        <a class="nav-item" href="<?php echo $_SERVER['PHP_SELF']; ?>?salir" style="margin-top:auto;"><i class="ti ti-logout" aria-hidden="true"></i> Cerrar sesión</a>
    </nav>