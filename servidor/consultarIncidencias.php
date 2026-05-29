<?php
    //ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos las variables de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como gestor, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["gestor"])) {
        header("Location: accesoAdministrador.php");
        exit();
    }

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas · Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Devuelve las iniciales de una cadena con distintas palabras
    function iniciales(string $nombre): string {
        $palabras = explode(' ', trim($nombre));
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                // Para cada palabra, nos quedamos con la primera letra y la transformamos a mayúscula
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    // Si hemos iniciado sesión como gestor
    if(!empty($_SESSION["gestor"])){

        $crud = new Crud(new DB("proyecto"));
        $incidencias = $crud->listar("*", "sugerencias_incidencias", " order by fecha");
        // Guardamos el gestor para que puedan mostrarse sus datos en la barra de navegación
        $gestor = $crud->obtener("gestores", "where email = \"$_SESSION[gestor]\"")[0];
        $fecha = new DateTime();
        // Formato de fecha en español
        $formatoFecha = new IntlDateFormatter(
            // fecha en español
            'es_ES',
            // Formato Martes, 12 de abril de 1952 d. C. o 15:30:42 h (hora del Pacífico)
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        // Guardamos las iniciales del nombre completo del gestor
        $iniciales = iniciales($gestor['nombre']);

?>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
        <main class="main">
            <!-- BIENVENIDA -->
            <div class="welcome-bar">
                <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
                <div class="welcome-text">
                    <h1>Bienvenida/o, <?php echo "$gestor[nombre]"; ?></h1>
                    <p>Hoy es <?php echo $formatoFecha->format($fecha);?></p>
                </div>
                <span class="badge badge-green">
                    <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
                </span>
            </div>
            <div class="card shadow-sm border-0">
                <div class="p-3 py-4">
<?php
        // Si no hay incidencias
        if($incidencias == null) {
?>
            <div class="seccionSubtitulo mb-4">
                <i class="ti ti-circle-number-0"></i>
                <div>
                    <h2>No hay incidencias enviadas por usuarios</h2>
                    <small class="text-muted">Cuando los usuarios envíen incidencias/sugerencias, podrás consultarlas aquí</small>
                </div>
            </div>
<?php 
                    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; 
        }
        // Si hay incidencias
        else {
?>
                <div class="seccionSubtitulo mb-4">
                    <i class="ti ti-mail"></i>
                    <div>
                        <h2>Incidencias de los usuarios</h2>
                        <small class="text-muted">Consulta las incidencias enviadas por los usuarios</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Contenido</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $cont = 1;
                            // Recorremos y mostramos las incidencias
                            foreach($incidencias as $incidencia){
                        ?>
                            <tr>
                                <th><?php echo $cont ?></th>
                                <td><?php echo $incidencia['fecha'] ?></td>
                                <!-- Ajustamos el ancho de la columna al contenido con style -->
                                <td style="width: 1%; white-space: nowrap;"><?php echo $incidencia['contenido'] ?></td>
                                <td style="width: 1%; white-space: nowrap;"><?php echo $incidencia['cliente'] ?></td>
                            </tr>
                        <?php 
                                $cont++;
                            } 
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </main>
    <!-- Cerramos la sección principal, creada en navGestor.php -->
</div>
<?php 
    }
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>