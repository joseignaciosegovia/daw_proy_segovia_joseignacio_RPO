<!-- La barra de navegación será una columna dentro del contenido principal de la página -->
<div class="col-3 d-flex align-items-center">
    <nav class="navbar bg-primary ms-5 my-2 mb-4">
        <div class="container-fluid py-2">
            <a class="navbar-brand mb-0 h1 link-light" href="http://localhost/proyecto/servidor/consultarIncidencias.php">Consultar incidencias</a>
        </div>
        <div class="container-fluid py-2">
            <a class="navbar-brand mb-0 h1 link-light" href="<?php echo $_SERVER['PHP_SELF']; ?>?salir">Cerrar sesión</a>
        </div>
    </nav>
</div>