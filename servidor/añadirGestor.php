<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . 'config.php';
    ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos las variables de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['administrador']);
        unset($_SESSION['gestor']);
    }

    // Si no hemos iniciado sesión como un gestor que además sea administrador, volvemos a la página de gestión de pistas
    if (!(!empty($_SESSION["gestor"]) && !empty($_SESSION["administrador"]))) {
        header("Location: intranet.php");
        exit();
    }

    // Actualizamos el título de la página
    $titulo = "Administración de gestores · Moral de Calatrava";
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

    // Función que comprueba si la cadena recibida está vacía
    function nombreNoVacio(&$nombre) {
        // Si el nombre del usuario está vacío, mostramos un error
        if (strlen($nombre) == 0) {
            error("Error el Nombre no puede estar en blanco");
        }

        // Ponemos la primera letra de cada palabra en mayúsculas
        $nombre = ucwords($nombre); 
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

    // Si pulsamos el botón de Crear Gestor
    if (isset($_POST['Crear'])) {
        $datos = json_decode($_POST['Crear']);
     
        // Trimamos el nombre
        $nombre = ucwords($datos->nombre);

        // Comprobamos si el nombre del usuario está vacío
        nombreNoVacio($nombre);

        $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);

        // Si el gestor introduce un telefono
        if($datos->telefono != null)
            $telefono = $datos->telefono;
        // Si el gestor no introduce un telefono
        else
            $telefono = 'null';

        // Si el gestor ha elegido un archivo como foto de perfil
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $nombreTmp = $_FILES['foto']['tmp_name'];

            // Obtenemos la extensión del archivo
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

            // Lista de extensiones permitidas
            $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
            // Si la extensión del archivo es una extensión válida de foto
            if (in_array(strtolower($ext), $extPermitidas)) {
                // Generar nombre único para la foto
                $nombreFinal = uniqid("img_") . "." . $ext;

                // Ruta en el servidor
                $rutaServidor = __DIR__ . "/.." . "/imagenes/" . $nombreFinal;

                // Ruta de la base de datos
                $rutaBD = "/imagenes/" . $nombreFinal;
                // Si podemos mover la foto a la ruta del servidor
                if (move_uploaded_file($nombreTmp, $rutaServidor)) {
                    // Añadimos la ruta de la imagen para añadirla al nuevo gestor en la base de datos
                    $foto = $rutaBD;
                // Si no se ha podido mover la foto al servidor
                } else {
                    echo "Error al mover el archivo";
                }
            // Si la extensión del archivo no es una extensión válida de foto
            } else {
                echo "Formato de imagen no permitido";
            }
        }
        // Si el gestor no introduce ninguna foto de perfil, se le asigna la foto de perfil vacío
        else
            $foto = "/imagenes/blank-profile-picture.png";

        // Añadimos el gestor en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->insertar("gestores", "\"$datos->email\", \"$contraseña\", \"$nombre\", \"$datos->dni\", $telefono, \"$foto\", $datos->administrador");

        header("Location: intranet.php");
    }
    // Si no pulsamos el botón Crear Gestor, mostramos la página
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
                        <i class="ti ti-user"></i>
                        <div>
                            <h2>Crear gestor</h2>
                            <small class="text-muted">Introduce los datos del nuevo gestor</small>
                        </div>
                    </div>
                    <form method="POST" name="añadirGestor" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="row mt-2">
                            <div class="col-12 col-sm-6">
                                <label for="nombre" class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="" placeholder="Nombre completo" required>
                                <div class="invalid-feedback">
                                    Introduzca un nombre válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <label for="email" class="labels">Email</label>
                                <input type="email" id="email" class="form-control" name="Email" value="" placeholder="correo@ejemplo.com" required autocomplete="off">
                                <div class="invalid-feedback">
                                    Introduzca un email válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-6">
                                <label for="contraseña" class="labels">Contraseña</label>
                                <input type="password" id="contraseña" class="form-control" name="Contraseña" pattern=".{8,}" value="" placeholder="Mínimo 8 caracteres" required>
                                <div class="form-text">
                                    La contraseña debe tener al menos 8 caracteres 
                                </div>
                                <div class="invalid-feedback">
                                    Introduzca una contraseña
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <label for="confirmarContraseña" class="labels">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="confirmarContraseña" name="Confirmar contraseña" value="" placeholder="Repite la contraseña" required>
                                <div class="invalid-feedback">
                                    Confirme la contraseña
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-6">
                                <label for="dni" class="labels">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" pattern="[0-9]{8}[A-Z]" value="" placeholder="12345678A" required>
                                <div class="invalid-feedback">
                                    Introduzca un DNI válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <label for="administrador" class="labels">¿Es administrador?</label><br>
                                <select class="form-select" id="administrador">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-6">
                                <label for="telefono" class="labels">Teléfono (opcional)</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" value="" placeholder="600 000 000">
                                <div class="invalid-feedback">
                                    Introduzca un teléfono válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                <label for="foto" class="labels">Foto de perfil (opcional)</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <div class="invalid-feedback">
                                    Introduzca una foto válida
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 d-flex justify-content-end">
                            <button class="btn btn-success profile-button" type="submit" name="Crear">Crear gestor</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-2 text-start">
                <button class="btn btn-secondary" onclick="window.location.href='administrarGestores.php';">Volver atrás</button>
            </div>
        </main>
        <!-- Cerramos la sección principal, creada en navGestor.php -->
    </div>
<?php
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>