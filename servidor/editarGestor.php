<?php
    // ob_start(); // activa el buffer
    session_start();

    // Si pulsamos el botón de cerrar sesión, borramos la variable de sesión
    if(isset($_GET['salir'])) {
        unset($_SESSION['gestor']);
        unset($_SESSION['administrador']);
    }

    // Si no hemos iniciado sesión como administrador, volvemos a la página de inicio de sesión de la intranet
    if (empty($_SESSION["administrador"])) {
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

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script type="module" src="/js/validacion.js"></script>
        <script type="module" src="/js/confirmacion.js"></script>
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

    // Si pulsamos el botón de actualizar
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
        else
            $valores .= ", telefono = null";

        // Si el gestor ha cambiado la contraseña, la añadimos a los valores para actualizar el gestor
        if($datos->contraseña != "") {
            $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);
            $valores .= ", contrasena = \"$contraseña\"";
        }

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

                    // Añadimos la ruta de la imagen para actualizar el cliente en la base de datos
                    $valores .= ", foto = '$rutaBD'";

                } else {
                    echo "Error al mover el archivo";
                }

            } else {
                echo "Formato de imagen no permitido";
            }
        }

        // Actualizamos el perfil en la base de datos
        $crud = new Crud(new DB("proyecto"));
        $condicion = "where email = \"$datos->email\"";
        $crud->actualizar("gestores", $valores, $condicion);
    }

    // Si pulsamos el botón de borrar
    elseif (isset($_POST['Borrar'])) {
        $crud = new Crud(new DB("proyecto"));
        $crud->eliminar("gestores", "where email = \"$_POST[Email]\"");
        // En confirmacion.js está el mensaje para confirmar el borrado
        header("Location: intranet.php");
    }

    // Si se obtiene la variable "gestor" (pulsando el botón "Editar gestor" de intranet.php)
    if(isset($_GET['gestor'])) {
        $crud = new Crud(new DB("proyecto"));
        $gestor = $crud->obtener("gestores", "where email = \"$_GET[gestor]\"")[0];
    
?>
    <!-- Creamos un container en el que estará la barra de navegación y el contenido principal de la página -->
    <div class="container-fluid">
        <div class="row">
            <!-- La barra de navegación será la primera columna -->
            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/navGestor.php"; ?>

            <!-- El contenido principal de la página será la segunda columna -->
            <div class="col-12 col-lg-8 d-flex align-items-center">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?gestor=" . $gestor['email']; ?>" name="editarGestor">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Gestor: <?php echo "$_GET[gestor]" ?></h4>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $gestor['nombre'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un nombre válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="labels">Contraseña</label>
                                <input type="password" class="form-control" id="contraseña" name="Contraseña" value="">
                                <div class="invalid-feedback">
                                    Introduzca una contraseña válida
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-7">
                                <label class="labels">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="confirmarContraseña" name="Confirmar contraseña" value="">
                                <div class="invalid-feedback">
                                    Confirme la contraseña
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" pattern="[0-9]{8}[A-Z]" value="<?php echo $gestor['DNI'] ?>">
                                <div class="invalid-feedback">
                                    Introduzca un DNI válido
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Teléfono (opcional)</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" value="<?php echo $gestor['telefono'] ?>">
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
                                    <option value="1" <?php if($gestor['administrador'] == 1) echo "selected" ?>>Sí</option>
                                    <option value="0" <?php if($gestor['administrador'] == 0) echo "selected" ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-12 col-lg-7">
                                <label class="labels">Foto de perfil</label>
                                <img class="img-thumbnail mb-2" name="foto" src="<?php echo $gestor["foto"] ?>" alt="Foto de perfil" width="100" height="100">
                                <input type="file" class="form-control" id="foto" name="foto">
                                <div class="invalid-feedback">
                                    Introduzca una foto válida
                                </div>
                                <div class="valid-feedback">
                                    Dato correcto
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit" name="Actualizar">Actualizar gestor</button>
                            <button class="btn btn-danger profile-button" name="Borrar">Borrar gestor</button>
                        </div>
                        <!-- Campo oculto para guardar el email del gestor para poder actualizarlo -->
                        <input id="email" name="email" type="hidden" value="<?php echo "$gestor[email]"?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="administrarGestores.php"><button>Volver atrás</button></a>

<?php
    }
    // Si no hemos llegado a esta página a través del botón "Editar gestor" de intranet.php, volvemos a dicha página
    else {
        header('Location: intranet.php');
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>