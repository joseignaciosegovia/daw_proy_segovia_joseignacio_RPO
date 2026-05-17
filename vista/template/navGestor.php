<!-- La barra de navegación será una columna dentro del contenido principal de la página -->
<div class="layout" id="seccionPrincipal">
    <nav class="sidebar" aria-label="Menú principal">
        <div class="user-chip">
            <div class="avatar"><img class="rounded-circle" src="<?php echo $gestor['foto'] ?>" alt="Foto de perfil" width="60" height="60" style="object-fit:cover;"></div>
            <div>
                <span><?php echo "$gestor[nombre]";  ?></span>
                <small>Usuario activo</small>
            </div>
        </div>
        <div class="nav-section">General</div>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/servidor/intranet.php") echo "active"; ?>" href="/servidor/intranet.php"><i class="ti ti-home" aria-hidden="true"></i> Inicio</a>
        <?php 
            if($_SESSION['administrador'] != null) {
        ?>
        <div class="nav-section">Administración</div>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/servidor/administrarGestores.php") echo "active"; ?>" href="/servidor/administrarGestores.php"><i class="ti ti-user" aria-hidden="true"></i> Administrar gestores</a>
        <?php } ?>
        <div class="nav-section">Consultas</div>
        <a class="nav-item <?php if($_SERVER['PHP_SELF'] == "/servidor/consultarIncidencias.php") echo "active"; ?>" href="/servidor/consultarIncidencias.php"><i class="ti ti-mail" aria-hidden="true"></i> Buzón de incidencias de los clientes</a>
        <div class="nav-section">Soporte</div>
        <a class="nav-item" href="<?php echo $_SERVER['PHP_SELF']; ?>?salir" style="margin-top:auto;"><i class="ti ti-logout" aria-hidden="true"></i> Cerrar sesión</a>
    </nav>