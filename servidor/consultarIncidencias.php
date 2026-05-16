<?php
    //ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
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
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
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

    // Si hemos iniciado sesión como gestor
    if(!empty($_SESSION["gestor"])){

        $crud = new Crud(new DB("proyecto"));
        $incidencias = $crud->listar("*", "sugerencias_incidencias", " order by fecha");
        // Guardamos el gestor para que puedan mostrarse sus datos en la barra de navegación
        $gestor = $crud->obtener("gestores", "where email = \"$_SESSION[gestor]\"")[0];
        $fecha = new DateTime();
        // Formato de fecha en español
        $formatter = new IntlDateFormatter(
            'es_ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $iniciales = iniciales($gestor['nombre']);

        if($incidencias == null) {
?>
            <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
            <div class="container-fluid">
                <div class="row">
                    <h1 class="d-flex justify-content-center">No hay incidencias enviadas por usuarios</h1>
                    <!-- La barra de navegación será la primera columna -->
<?php 
                    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; 
        }
        else {

?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <main class="main">
         <!-- BIENVENIDA -->
            <div class="welcome-bar">
                <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
                <div class="welcome-text">
                    <h1>Bienvenida, <?php echo "$gestor[nombre]"; ?></h1>
                    <p>Hoy es <?php echo $formatter->format($fecha);?> &middot; Usuario activo</p>
                </div>
                <span class="badge badge-green">
                    <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
                </span>
            </div>
            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8 d-flex align-items-center">
                <h1 class="d-flex justify-content-center">Incidencias de los usuarios</h1>
                <table class="table table-striped table-hover">
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
                            <td><?php echo $incidencia['contenido'] ?></td>
                            <td><?php echo $incidencia['cliente'] ?></td>
                        </tr>
                    <?php 
                            $cont++;
                        } 
                    ?>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary form-floating" onclick="window.location.href='intranet.php';">Volver atrás</button>
    </main>
    </div>
 <?php   
        } 
    }
?>

<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>