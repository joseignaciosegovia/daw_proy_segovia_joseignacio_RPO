<!DOCTYPE html>
<html lang="es">
    <head>
        <!-- Metadatos -->
        <meta charset="UTF-8">
        <meta name="author" content="José Ignacio Segovia Ramírez">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Titulo -->
        <title><?php echo isset($titulo) ? $titulo : "Reserva de pistas · Moral de Calatrava"?></title>
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/imagenes/Moral.png">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Iconos -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <!-- Hoja de estilos -->
        <link rel="stylesheet" type="text/css" href="/css/estilos.css">
        <!-- Animanate CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <!-- Cada página añadirá los scripts que necesite -->
        <?php if (function_exists('añadirScriptsCabecera')){
            añadirScriptsCabecera();
        }?>
    </head>
    <body>
        <header>
            <div class="container-fluid" id="cabecera">
                <div class="row d-flex align-items-center py-2">
                    <div class="col-auto">
                        <a href=<?php echo isset($home) ? $home : "/index.php"?> ><img src="/imagenes/Moral2.png" alt="Logo de la página"></a>
                    </div>
                    <div class="col col-lg-9 fs-1">
                        <!-- El título contiene un enlace a la página principal -->
                        <div class="cabeceraTitulo">
                            <a class="text-decoration-none" href=<?php echo isset($home) ? $home : "/index.php"?> ><?php echo isset($titulo) ? $titulo : "Reserva de pistas · Moral de Calatrava"?></a>
                        </div>
                        <div class="cabeceraSubtitulo">Polideportivo y Ciudad Deportiva</div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary btn-intranet form-floating" onclick="window.location.href='/servidor/accesoAdministrador.php';">Intranet</button>
                    </div>
                    <!-- Si estamos en una página que tiene la barra de navegación, mostramos el botón hamburguesa (solo en pantallas móviles) -->
                    <?php if($_SERVER['PHP_SELF'] != "/index.php" && $_SERVER['PHP_SELF'] != "/verificar.php" && $_SERVER['PHP_SELF'] != "/public/accesoCliente.php" && $_SERVER['PHP_SELF'] != "/servidor/accesoAdministrador.php") { ?>
                    <div class="col-auto">
                        <button id="btnMenu" onclick="desplegarMenu()">
                            <i class="ti ti-menu-2"></i>
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </header>