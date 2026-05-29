<?php
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos las variables de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como un gestor que además sea administrador, volvemos a la página de inicio
    if (!(!empty($_SESSION["gestor"]) && !empty($_SESSION["administrador"]))) {
        header("Location: intranet.php");
        exit();
    }

    // Si no hemos llegado a esta página de las maneras adecuadas, volvemos a intranet.php
    if (!isset($_POST['Actualizar']) && !isset($_GET['Borrar']) && ! isset($_GET['gestorEditar'])) {
        header('Location: intranet.php');
        die();
    }

    // Actualizamos el título de la página
    $titulo = "Administración de gestores · Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    use Clases\DB;

    // Si pulsamos el botón de Borrar
    if (isset($_GET['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));
        $foto = $crud->obtener("gestores", "where email = \"$_GET[Borrar]\"")[0]['foto'];
        // Si la foto anterior existe y no es la foto por defecto de perfil vacío
        if ($foto && $foto != "/imagenes/blank-profile-picture.png") {
            $rutaFoto = __DIR__ . "/.." . $foto;
            if (file_exists($rutaFoto)) {
                // Borramos del servidor la foto
                unlink($rutaFoto);
            }
        }
        // Borramos el gestor de la base de datos
        $crud->eliminar("gestores", "where email = \"$_GET[Borrar]\"");
        header("Location: administrarGestores.php");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

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

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <script type="module" src="/js/borrarGestor.js"></script>
<?php }

    // Función que comprueba si la cadena recibida está vacía
    function nombreNoVacio(&$nombre) {
        // Si el nombre del usuario está vacío, mostramos un error
        if (strlen($nombre) == 0) {
            error("Error el Nombre no puede estar en blanco");
        }

        // Ponemos la primera letra de cada palabra en mayúsculas
        $nombre = ucwords($nombre); 
    }

    // Si pulsamos el botón de Actualizar
    if (isset($_POST['Actualizar'])) {
        $datos = json_decode($_POST['Actualizar']);
     
        // Trimamos el nombre
        $nombre = ucwords($datos->nombre);

        // Comprobamos si el nombre del usuario está vacío
        nombreNoVacio($nombre);

        $valores = "nombre = \"$nombre\", DNI = \"$datos->dni\", administrador = $datos->administrador";

        // Si el gestor introduce un telefono
        if($datos->telefono != null)
            $valores .= ", telefono = $datos->telefono";
        // Si el gestor no introduce un telefono
        else
            $valores .= ", telefono = null";

        // Si el gestor ha cambiado la contraseña, la añadimos a los valores para actualizar el gestor
        if($datos->contraseña != "") {
            $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);
            $valores .= ", contrasena = \"$contraseña\"";
        }

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
                    // Obtenemos la foto anterior antes de actualizar la base de datos
                    $crud = new Crud(new DB("proyecto"));
                    $fotoAntigua = $crud->obtener("gestores", "where email = \"$_SESSION[gestor]\"")[0]['foto'];
                    // Si la foto anterior existe y no es la foto por defecto de perfil vacío
                    if ($fotoAntigua && $fotoAntigua != "/imagenes/blank-profile-picture.png") {
                        $rutaFotoAntigua = __DIR__ . "/.." . $fotoAntigua;
                        if (file_exists($rutaFotoAntigua)) {
                            // Borramos del servidor la foto anterior
                            unlink($rutaFotoAntigua);
                        }
                    }
                    // Añadimos la ruta de la imagen para actualizar el gestor en la base de datos
                    $valores .= ", foto = '$rutaBD'";
                // Si no se ha podido mover la foto al servidor
                } else {
                    echo "Error al mover el archivo";
                }
            // Si la extensión del archivo no es una extensión válida de foto
            } else {
                echo "Formato de imagen no permitido";
            }
        }

        // Actualizamos el perfil del gestor en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $condicion = "where email = \"$datos->email\"";
        $crud->actualizar("gestores", $valores, $condicion);
    }

    // Si se obtiene la variable "gestorEditar" (pulsando el botón "Editar gestor" de administrarGestores.php)
    if(isset($_GET['gestorEditar'])) {
        $crud = new Crud(new DB("proyecto"));
        // Guardamos el gestor para que puedan mostrarse sus datos en la barra de navegación
        $gestor = $crud->obtener("gestores", "where email = \"$_SESSION[gestor]\"")[0];
        // Guardamos los datos del gestor que vamos a editar
        $gestorEditar = $crud->obtener("gestores", "where email = \"$_GET[gestorEditar]\"")[0];
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
                        <h2>Gestor/a <?php echo "$gestorEditar[nombre]" ?></h2>
                        <small class="text-muted">Modifica los datos del gestor <?php echo "$gestorEditar[nombre]" ?></small>
                    </div>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?gestorEditar=" . $gestorEditar['email']; ?>" name="editarGestor">
                    <div class="row mt-2">
                        <div class="col-12 col-sm-6">
                            <label for="nombre" class="labels">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $gestorEditar['nombre'] ?>" placeholder="Nombre completo" required>
                            <div class="invalid-feedback">
                                Introduzca un nombre válido
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                            <label for="dni" class="labels">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" pattern="[0-9]{8}[A-Z]" value="<?php echo $gestorEditar['DNI'] ?>" placeholder="12345678A" required>
                            <div class="invalid-feedback">
                                Introduzca un DNI válido
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-sm-6">
                            <label for="contraseña" class="labels">Contraseña</label>
                            <input type="password" class="form-control" id="contraseña" name="Contraseña" pattern=".{8,}" value="" placeholder="Mínimo 8 caracteres">
                            <div class="form-text">
                                La contraseña debe tener al menos 8 caracteres 
                            </div>
                            <div class="invalid-feedback">
                                Introduzca una contraseña válida
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                            <label for="confirmarContraseña" class="labels">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmarContraseña" name="Confirmar contraseña" value="" placeholder="Repite la contraseña">
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
                            <label for="telefono" class="labels">Teléfono (opcional)</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" value="<?php echo $gestorEditar['telefono'] ?>" placeholder="600 000 000">
                            <div class="invalid-feedback">
                                Introduzca un teléfono válido
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                            <label for="foto" class="labels">Foto de perfil (opcional)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img class="rounded-circle" src="<?php echo $gestorEditar["foto"] ?>" alt="Foto de perfil" width="60" height="60" style="object-fit:cover;">
                                <span class="text-muted small">Foto actual</span>
                            </div>
                            <input type="file" class="form-control" id="foto" name="foto">
                            <div class="invalid-feedback">
                                Introduzca una foto válida
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-sm-6">
                            <label for="administrador" class="labels">¿Es administrador?</label><br>
                            <select class="form-select" id="administrador">
                                <option value="1" <?php if($gestorEditar['administrador'] == 1) echo "selected" ?>>Sí</option>
                                <option value="0" <?php if($gestorEditar['administrador'] == 0) echo "selected" ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button class="btn btn-success profile-button" type="submit" name="Actualizar">Actualizar gestor</button>
                    </div>
                    <!-- Campo oculto para guardar el email del gestor para poder actualizarlo -->
                    <input id="email" name="email" type="hidden" value="<?php echo "$gestorEditar[email]"?>">
                </form>
            
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="Email" value="<?php echo $gestorEditar['email']; ?>">
                    <div class="mt-3 text-center">
                        <button class="btn btn-danger profile-button" type="submit" name="Borrar" value="1">Borrar gestor</button>
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
    }
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>