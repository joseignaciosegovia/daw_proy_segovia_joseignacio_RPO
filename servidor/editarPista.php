<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . 'config.php';
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

    // Si no hemos llegado a esta página de la manera adecuada
    if (!isset($_GET['Borrar']) && !isset($_POST['datos']) && !isset($_GET['pista'])) {
        header('Location: intranet.php');
        die();
    }

    // Actualizamos el título de la página
    $titulo = "Gestión de pistas y reservas · Moral de Calatrava";
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
                // Para cada palabra, nos quedamos con la primera letra y la transformamos a mayúscula
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
    $formatoFecha = new IntlDateFormatter(
        // fecha en español
        'es_ES',
        // Formato Martes, 12 de abril de 1952 d. C. o 15:30:42 h (hora del Pacífico)
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE
    );
    // Guardamos las iniciales del nombre completo del gestor
    $iniciales = iniciales($gestor['nombre']);

    // Si pulsamos el botón de Borrar
    if (isset($_GET['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("pistas", "where id = $_GET[Borrar]");
        // En borrarPista.js está el mensaje para confirmar el borrado
        header("Location: intranet.php");
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <script type="module" src="/js/borrarPista.js"></script>
<?php }

    // Si pulsamos el botón de Actualizar
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $valores = "nombre = \"$datos->nombre\", localizacion = \"$datos->localizacion\", precioReserva = '$datos->precio'";        
        $condicion = "where id = $datos->id";

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->actualizar("pistas", $valores, $condicion);
        // Volvemos a guardar el nombre de la pista para mostrar la página
        $_GET['pista'] = $datos->nombre;
    }

    // Si se obtiene la variable "pista" (pulsando el botón "Editar" de intranet.php), mostramos la página
    if(isset($_GET['pista'])) {
        $crud = new Crud(new DB("proyecto"));
        $pista = $crud->obtener("pistas", "where id = $_GET[pista]")[0];

        require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php";
    
?>
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
                <div class="seccionSubtitulo mb-4">
                    <i class="ti ti-soccer-field"></i>
                    <div>
                        <h2>Editar la pista <?php echo $pista['nombre'] ?></h2>
                        <small class="text-muted">Modifica los datos de la pista <?php echo $pista['nombre'] ?></small>
                    </div>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?pista=" . $pista['id']; ?>" name="editarPista">
                    <div class="p-3 py-5">
                        <div class="row mt-3">
                            <div class="col-12 col-sm-6">
                                <label for="nombre" class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la pista" value="<?php echo $pista['nombre'] ?>" required>
                                <div class="invalid-feedback">
                                    Introduzca un nombre válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <label for="precio" class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" id="precio" name="precio" placeholder="0,00" step="0.01" value="<?php echo $pista['precioReserva'] ?>" required>
                                <div class="invalid-feedback">
                                    Introduzca un precio válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-6">
                                <label for="Localizacion">Localización</label><br>
                                <select class="form-select" name="Localizacion" id="Localizacion">
                                    <?php
                                        // Obtenemos y recorremos las localizaciones
                                        $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                                        foreach($localizaciones as $localizacion){
                                            // Añadimos cada localización al select
                                            echo "<option value=\"$localizacion[localizacion]\"";
                                            // La opción indicada por defecto será la localización actual de la pista
                                            if($pista['localizacion'] == $localizacion['localizacion']){
                                                echo " selected";
                                            }
                                            echo ">$localizacion[localizacion]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-success profile-button" type="submit" name="Actualizar">Actualizar pista</button>
                            <button class="btn btn-danger profile-button" name="Borrar" id="borrar">Borrar pista</button>
                        </div>
                        <!-- Campo oculto para guardar el id de la pista para poder actualizarla -->
                        <input id="id" type="hidden" value="<?php echo "$pista[id]"?>">
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-2 text-start">
            <button class="btn btn-secondary" onclick="window.location.href='intranet.php';">Volver atrás</button>
        </div>
    </main>
    <!-- Cerramos la sección principal, creada en navGestor.php -->
</div>
<?php
    }
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>