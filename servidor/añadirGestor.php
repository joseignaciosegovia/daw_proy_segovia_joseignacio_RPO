<?php
    ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
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
    $titulo = "Gestión de pistas y reservas | Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
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

    $crud = new Crud(new DB("proyecto"));

    // Si pulsamos el botón de crear Gestor
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
        else
            $telefono = 'null';

        // Si el gestor ha elegido una imagen
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

                    // Añadimos la ruta de la imagen para añadirla al nuevo gestor en la base de datos
                    $foto = $rutaBD;

                } else {
                    echo "Error al mover el archivo";
                }

            } else {
                echo "Formato de imagen no permitido";
            }
        }
        // Si el usuario no introduce ninguna foto de perfil, se le asigna la foto de perfil vacío
        else
            $foto = "/imagenes/blank-profile-picture.png";

        // Añadimos el gestor en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $crud->insertar("gestores", "\"$datos->email\", \"$contraseña\", \"$nombre\", \"$datos->dni\", $telefono, \"$foto\", $datos->administrador");

        header("Location: intranet.php");
    }
?>
        <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
        <div class="container-fluid">
            <div class="row">
                <!-- La barra de navegación será la primera columna -->
                <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

                <!-- El contenido principal de la página será la segunda columna -->
                <div class="col-12 col-lg-8 d-flex align-items-center">
                    <form method="POST" name="añadirGestor" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Crear gestor</h4>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="" required>
                                <div class="invalid-feedback">
                                    Introduzca un nombre válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label class="labels">Email</label>
                                    <input type="email" id="email" class="form-control" name="Email" value="" required>
                                    <div class="invalid-feedback">
                                        Introduzca un email válido
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                   <label class="labels">Contraseña</label>
                                    <input type="password" id="contraseña" class="form-control" name="Contraseña" value="" required>
                                    <div class="invalid-feedback">
                                        Introduzca una contraseña
                                    </div>
                                    <div class="valid-feedback">
                                        Dato correcto
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-7">
                                <label class="labels">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="confirmarContraseña" name="Confirmar contraseña" value="" required>
                                <div class="invalid-feedback">
                                    Confirme la contraseña
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" pattern="[0-9]{8}[A-Z]" value="" required>
                                <div class="invalid-feedback">
                                    Introduzca un DNI válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Teléfono (opcional)</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" value="">
                                <div class="invalid-feedback">
                                    Introduzca un teléfono válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="labels" for="administrador">¿Es administrador?</label>
                                <select id="administrador">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-lg-7">
                                <label class="labels">Foto de perfil</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <div class="invalid-feedback">
                                    Introduzca una foto válida
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="Crear">Crear gestor</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <a href="administrarGestores.php"><button>Volver atrás</button></a>
    </div>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>