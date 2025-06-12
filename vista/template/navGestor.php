<!-- La barra de navegación será una columna dentro del contenido principal de la página -->
<div class="col-8 col-sm-6 col-md-4 col-lg-4 d-flex align-items-center">
    <nav class="navbar col-12 col-sm-9 col-lg-8 bg-primary ms-5 my-2 mb-4">
        <div class="container-fluid py-2">
            <a class="navbar-brand mb-0 h1 link-light" href="/servidor/consultarIncidencias.php">Consultar incidencias</a>
        </div>
        <div class="container-fluid py-2">
            <a class="navbar-brand mb-0 h1 link-light" href="<?php echo $_SERVER['PHP_SELF']; ?>?salir">Cerrar sesión</a>
        </div>
    </nav>
</div>