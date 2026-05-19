<?php
    // ob_start(); // activa el buffer
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

    // Si no hemos llegado a esta página de la manera adecuada
    if (!isset($_GET['Borrar']) && !isset($_POST['datos']) && !isset($_GET['pista'])) {
        header('Location: intranet.php');
        die();
    }

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
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
                $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        return $iniciales;
    }

    $crud = new Crud(new DB("proyecto"));
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

    // Si pulsamos el botón de borrar
    if (isset($_GET['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));

        $id = $crud->listar("id", "pistas", "where nombre = \"$_GET[Borrar]\"")[0]['id'];
        $crud->eliminar("pistas", "where id = $id");
        // En confirmacion.js está el mensaje para confirmar el borrado
        header("Location: intranet.php");
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <script type="module" src="/js/confirmacion.js"></script>
<?php }

    // Si pulsamos el botón de actualizar
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $valores = "nombre = \"$datos->nombre\", localizacion = \"$datos->localizacion\", precioReserva = '$datos->precio'";        
        $condicion = "where id = $datos->id";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("pistas", $valores, $condicion);

        $_GET['pista'] = $datos->nombre;
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Editar" de intranet.php)
    if(isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));
        $pista = $crud->obtener("pistas", "where id = $_GET[pista]")[0];
    
?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>
    <main class="main">
        <!-- BIENVENIDA -->
        <div class="welcome-bar">
            <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
            <div class="welcome-text">
                <h1>Bienvenida/o, <?php echo "$gestor[nombre]"; ?></h1>
                <p>Hoy es <?php echo $formatter->format($fecha);?> &middot; Usuario activo</p>
            </div>
            <span class="badge badge-green">
                <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
            </span>
        </div>

        <div class="card shadow-sm border-0">
            <div class="p-3 py-4">
                <div class="section-header mb-4">
                    <i class="ti ti-soccer-field"></i>
                    <div>
                        <h2>Editar la pista <?php echo $pista['nombre'] ?></h2>
                        <small class="text-muted">Modifica los datos de la pista <?php echo $pista['nombre'] ?></small>
                    </div>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?pista=" . $pista['id']; ?>" name="editarPista">
                    <div class="p-3 py-5">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="nombre" class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $pista['nombre'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un nombre válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="Localizacion">Localización</label><br>
                                <select name="Localizacion" id="Localizacion">
                                    <?php
                                        // Obtenemos y recorremos las localizaciones
                                        $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                                        foreach($localizaciones as $localizacion){
                                            // Añadimos cada localización al select
                                            echo "<option value=\"$localizacion[localizacion]\"";
                                            // La opción indicada por defecto será la localización de la pista
                                            if($pista['localizacion'] == $localizacion['localizacion']){
                                                echo " selected";
                                            }
                                            echo ">$localizacion[localizacion]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <hr class="mt-0 mb-4" style="border-color: #dee2e6;">
                        <div class="row mt-3">
                            <div class="col-md-12 mt-3">
                                <label for="precio" class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?php echo $pista['precioReserva'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un precio válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-success profile-button" type="submit" name="Actualizar">Actualizar pista</button>
                            <button class="btn btn-danger profile-button" name="Borrar" id="borrar">Borrar pista</button>
                        </div>
                        <!-- Campo oculto para guardar el id de la pista para poder actualizarlo -->
                        <input id="id" type="hidden" value="<?php echo "$pista[id]"?>">
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-2 text-start">
            <button class="btn btn-secondary" onclick="window.location.href='intranet.php';">Volver atrás</button>
        </div>
    </main>
</div>

<?php
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>