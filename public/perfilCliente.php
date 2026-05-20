<?php
    //ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['cliente']);
    }

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/estilosBienvenida.css">
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }

    // Función que guarda un mensaje de error (en caso de que haya habido algún problema) y actualiza la página
    function error($mensaje) {
        $_SESSION['error'] = $mensaje;
        header('Location: index.php');
        die();
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

    // Si no hemos iniciado sesión como cliente, volvemos a la página de inicio
    if (empty($_SESSION["cliente"])) {
        header("Location: ../index.php");
        exit();
    }

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de actualizar perfil
    if (isset($_POST['datos'])) {
        $datos = json_decode($_POST['datos']);

        // Trimamos el nombre
        $nombre = ucwords($datos->nombre);

        // Comprobamos si el nombre del usuario está vacío
        nombreNoVacio($nombre);

        $valores = "nombre = \"$nombre\", DNI = \"$datos->dni\"";

        // Si el usuario introduce un telefono
        if($datos->telefono != null)
            $valores .= ", telefono = $datos->telefono";
        else
            $valores .= ", telefono = null";

        // Si el usuario ha cambiado la contraseña, la añadimos a los valores para actualizar el usuario
        if($datos->contraseña != "") {
            $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);
            $valores .= ", contrasena = \"$contraseña\"";
        }

        // Si el usuario ha elegido una imagen
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $nombreTmp = $_FILES['foto']['tmp_name'];

            // Obtener extensión real
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

            // Lista de extensiones permitidas
            $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($ext), $extPermitidas)) {

                // Generar nombre único
                $nombreFinal = uniqid("img_") . "." . $ext;

                // Ruta en el servidor
                $rutaServidor = __DIR__ . "/.." . "/imagenes/" . $nombreFinal;

                // Ruta de la base de datos (para mostrar en HTML)
                $rutaBD = "/imagenes/" . $nombreFinal;

                if (move_uploaded_file($nombreTmp, $rutaServidor)) {

                    // Añadimos la ruta de la imagen para actualizar el cliente en la base de datos
                    $valores .= ", foto = '$rutaBD'";

                } else {
                    echo "Error al mover el archivo";
                }

            } else {
                echo "Formato de imagen no permitido";
            }
        }

        $condicion = "where email = \"$_SESSION[cliente]\"";

        // Actualizamos el perfil en la base de datos
        $crud->actualizar("clientes", $valores, $condicion);
    }
    
    // Cargamos la cabecera
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Guardamos el cliente para que puedan mostrarse sus datos en la barra de navegación
    $cliente = $crud->obtener("clientes", "where email = \"$_SESSION[cliente]\"")[0];
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navCliente.php";
    // Datos que vamos a mostrar
    $fecha = new DateTime();
    // Formato de fecha en español
    $formatter = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE
    );
    $iniciales = iniciales($cliente['nombre']);

    // Si ha habido algún error, lo mostramos antes que la información principal de la página
    if (isset($_SESSION['error'])) {
        echo "<div class='mt-3 text-danger font-weight-bold text-lg d-flex justify-content-center'>";
        echo $_SESSION['error'];
        echo "</div>";
        // Borramos la variable para no volver a mostrar el error
        unset($_SESSION['error']);
    }
?>

    <!-- El contenido principal de la página será la segunda columna -->
    <main class="main">
        <div class="welcome-bar">
            <div class="welcome-avatar"><?php echo "$iniciales"; ?></div>
            <div class="welcome-text">
                <h1>Bienvenida/o, <?php echo "$cliente[nombre]"; ?></h1>
                <p>Hoy es <?php echo $formatter->format($fecha);?></p>
            </div>
            <span class="badge badge-green">
                <i class="ti ti-circle-check" aria-hidden="true"></i> Sesión activa
            </span>
        </div>
        <div class="card shadow-sm border-0">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="perfilCliente" enctype="multipart/form-data">
                <div class="p-3 py-4">
                    <div class="section-header mb-4">
                        <i class="ti ti-user"></i>
                        <div>
                            <h2>Información del perfil</h2>
                            <small class="text-muted">Modifica tus datos personales y de acceso</small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6 col-m3">
                            <label for="nombre" class="labels">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" name="Nombre" placeholder="Nombre completo" value="<?php echo $cliente['nombre'] ?>" required>
                            <div class="invalid-feedback">
                                Introduzca un nombre
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="dni" class="labels">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" placeholder="12345678A" pattern="[0-9]{8}[A-Z]" value="<?php echo $cliente['DNI'] ?>" required>
                            <div class="invalid-feedback">
                                Introduzca un DNI válido
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0 mb-4" style="border-color: #dee2e6;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="contraseña" class="labels">Contraseña</label>
                            <input type="password" class="form-control" id="contraseña" name="Contraseña" placeholder="Mínimo 8 caracteres" pattern=".{8,}" value="">
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
                        <div class="col-md-6">
                            <label for="confirmarContraseña" class="labels">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmarContraseña" name="Confirmar contraseña" placeholder="Repite la contraseña" value="">
                            <div class="invalid-feedback">
                                Confirme la contraseña
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0 mb-4" style="border-color: #dee2e6;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="telefono" class="labels">Teléfono (opcional)</label>
                            <input type="tel" class="form-control" id="telefono" name="Telefono" placeholder="600 000 000" pattern="[0-9]{9}" value="<?php echo $cliente['telefono'] ?>">
                            <div class="invalid-feedback">
                                Introduzca un número de teléfono válido
                            </div>
                            <div class="valid-feedback">
                                Dato correcto
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="foto" class="labels">Foto de perfil (opcional)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img class="rounded-circle" src="<?php echo $cliente['foto'] ?>" alt="Foto de perfil" width="60" height="60" style="object-fit:cover;">
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
                    <div class="mt-4 d-flex justify-content-end">
                        <button class="btn btn-success px-4" type="submit" name="Actualizar"><i class="bi bi-check-lg me-2"></i>Actualizar perfil</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    
<?php
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>