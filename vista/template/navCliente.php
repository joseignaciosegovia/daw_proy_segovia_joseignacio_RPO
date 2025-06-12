<!-- La barra de navegación será una columna dentro del contenido principal de la página -->
<div class="row" id="seccionPrincipal">
    <div class="col-8 col-sm-6 col-md-5 col-lg-4">
        <nav class="navbar bg-primary col-12 col-sm-9 col-lg-8 ms-5 my-2 mb-4">
            <div class="container-fluid py-2">
                <a class="navbar-brand mb-0 h1 link-light" href="http://localhost/public/perfilCliente.php">Datos personales</a>
            </div>
            <div class="container-fluid py-2">
                <a class="navbar-brand mb-0 h1 link-light" href="http://localhost/public/reservarPista.php">Nueva reserva</a>
            </div>
            <div class="container-fluid py-2">
                <a class="navbar-brand mb-0 h1 link-light" href="http://localhost/public/reservasCliente.php">Mis reservas</a>
            </div>
            <div class="container-fluid py-2">
                <a class="navbar-brand mb-0 h1 link-light" href="http://localhost/public/incidenciasQuejas.php">Buzón de incidencias</a>
            </div>
            <div class="container-fluid py-2">
                <a class="navbar-brand mb-0 h1 link-light" href="<?php echo $_SERVER['PHP_SELF']; ?>?salir">Cerrar sesión</a>
            </div>
        </nav>
    </div>