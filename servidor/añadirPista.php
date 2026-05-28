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
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
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

    // Si pulsamos el botón de crear pista
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        $localizacion = $datos->localizacion[0];
        $valores = "\"$datos->nombre\", \"$localizacion\", \"$datos->precio\"";

        // Añadimos la pista en la base de datos
        $crud->insertarColumnas("pistas", "(nombre, localizacion, precioReserva)", $valores);
        header("Location: intranet.php");
    }

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
                    <div class="seccionSubtitulo mb-4">
                        <i class="ti ti-soccer-field"></i>
                        <div>
                            <h2>Crear pista</h2>
                            <small class="text-muted">Introduce los datos de la nueva pista</small>
                        </div>
                    </div>
                    <form method="POST" name="añadirPista" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="row mt-2">
                            <div class="col-12 col-sm-6">
                                <label for="nombre" class="labels">Nombre</label>
                                <input type="text" id="nombre" class="form-control" name="Nombre" value="" placeholder="Nombre de la pista" required>
                                <div class="invalid-feedback">
                                    Introduzca un nombre
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <label for="precio" class="labels">Precio de Reserva</label>
                                <input type="number" class="form-control" id="precio" name="Precio" value="" placeholder="0,00" step="0.01" required>
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
                                        $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                                        // Añadimos las localizaciones a las opciones del select
                                        foreach($localizaciones as $localizacion){
                                            echo "<option value=\"$localizacion[localizacion]\">$localizacion[localizacion]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-5 text-center"><button class="btn btn-success profile-button" type="submit" name="Crear">Crear pista</button></div>
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
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>