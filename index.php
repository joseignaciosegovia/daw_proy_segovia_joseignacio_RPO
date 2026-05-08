<?php
    ob_start(); // activa el buffer
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    require_once 'config.php';

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <script type="module" src="/js/validacion.js"></script>
<?php }

    // Función para añadir scripts en el pie
    function añadirScriptsPie(){
?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
        <script type="module" src="/js/calendarioSinCliente.js"></script>
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

    // Si hemos iniciado sesión como cliente, mostramos la página de reservar pistas
    if (!empty($_SESSION["cliente"])) {
        header("Location: ./public/reservarPista.php");
        exit();
    }

    // Si pulsamos el botón "Crear Usuario"
    if (isset($_POST['datos'])) {
        // Recibimos los datos de JavaScript después de hacer la validación del submit
        $datos = json_decode($_POST['datos']);
        $crud = new Crud(new DB("proyecto"));

        // Trimamos las cadenas
        $nombre = trim($datos->nombre);
        $email = trim($datos->email);

        // Si el usuario ha introducido un teléfono
        if($datos->telefono == null)
            $telefono =  'null';
        else
            $telefono = $datos->telefono;

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
                $rutaServidor = __DIR__ . "/imagenes/" . $nombreFinal;

                // Ruta de la base de datos (para mostrar en HTML)
                $rutaBD = "/imagenes/" . $nombreFinal;

                if (move_uploaded_file($nombreTmp, $rutaServidor)) {

                    // Añadimos la ruta de la imagen para actualizar el cliente en la base de datos
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

        // Comprobamos si el nombre del usuario está vacío
        nombreNoVacio($nombre);

        // Comprobamos si ya existe un usuario con el email introducido
        $respuesta = $crud->obtener("clientes", "where email = \"$email\"");
        if($respuesta != null) {
            error("El email está repetido");
        }

        // Comprobamos si la contraseña coincide con la confirmación de la contraseña
        if($datos->contraseña != $datos->confirmarContraseña){
            error("La contraseña tiene que coincidir");
        }

        // Guardamos en una variable la contraseña cifrada
        $contraseña = password_hash($datos->contraseña, PASSWORD_DEFAULT);
        $codigo = password_hash(rand(0,1000), PASSWORD_DEFAULT);
        // Insertamos el usuario en la base de datos
        $crud->insertar("clientes", "\"$email\", \"$contraseña\", \"$nombre\", \"$datos->dni\", $telefono, \"$foto\", \"$codigo\", 0");

        // Dominio: reservapistasonline.moral.daw
        // GoDaddy: ns59.domaincontrol.com ns60.domaincontrol.com

        $body = json_encode([
            'from'    => 'Tu App <onboarding@resend.dev>',
            'to'      => [$email],
            'subject' => 'Verifica tu cuenta',
            'html'    => "
                <h2>Hola, $email</h2>
                <p>Pincha en el siguiente enlace para verificar tu cuenta:</p>
                <a href=\"http://localhost:8080/verificar.php?email=$email&codigo=$codigo\">Código de verificación</a>
                <p style='color:#64748b; font-size:13px;'>
                    Si no creaste esta cuenta, ignora este mensaje.
                </p>
            ",
        ]);

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . RESEND_API_KEY,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        $_SESSION['mensaje'] = 'Cliente creado Correctamente';
        //$_SESSION['cliente'] = $email;
    } else {
        // Si ha habido algún error, lo mostramos antes que la información principal de la página
        if (isset($_SESSION['error'])) {
            echo "<div class='mt-3 text-danger font-weight-bold text-lg d-flex justify-content-center'>";
            echo $_SESSION['error'];
            echo "</div>";
            // Borramos la variable para no volver a mostrar el error
            unset($_SESSION['error']);
        }
?>

    <div class="container-fluid my-3">
        <div class="row column-gap-3">
            <div class="col-12 col-md-5 pt-4" id="informacionPrincipal">
                <div class="row px-4">
                    <h2>Reservar pistas del polideportivo y de la Ciudad Deportiva</h2>
                    <p>En esta página podrás registrarte para reservar las pistas del polideportivo y de la Ciudad Deportiva de Moral de Calatrava</p>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <h2 class="d-flex justify-content-center">Registrarse</h2>
                <a class="btn btn-secondary my-2 d-flex justify-content-center" href="public/accesoCliente.php">Si ya tienes cuenta, inicia sesión aquí</a>
                
                <form class="row needs-validation px-4" name="crearUsuario" novalidate>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre Completo" required>
                        <div class="invalid-feedback">
                            Introduzca un nombre
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" placeholder="Correo electrónico" autocomplete="off" required>
                        <div class="invalid-feedback">
                            Introduzca un correo electrónico válido
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contraseña" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contraseña" placeholder="Contraseña" pattern=".{8,}" required>
                        <div id="passwordHelpBlock" class="form-text">
                            La contraseña debe tener al menos 8 caracteres 
                        </div>
                        <div class="invalid-feedback">
                            Introduzca una contraseña válida
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmarContraseña" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="confirmarContraseña" placeholder="Contraseña" required>
                        <div class="invalid-feedback">
                            Confirme la contraseña
                        </div>
                        <div class="valid-feedback">
                            La contraseña coincide
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="dni" placeholder="DNI" pattern="[0-9]{8}[A-Z]">
                        <div class="invalid-feedback">
                            Introduzca un DNI válido
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" placeholder="Teléfono (opcional)" pattern="[0-9]{9}">
                        <div class="invalid-feedback">
                            Introduzca un número de teléfono válido
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto de perfil</label>
                        <input type="file" class="form-control" id="foto">
                        <div class="invalid-feedback">
                            Introduzca una imagen válida
                        </div>
                        <div class="valid-feedback">
                            Dato correcto
                        </div>
                    </div>
                    <div class="mb-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary form-floating" name="crear">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-8 col-sm-6">
            <h2 class="d-flex justify-content-center">Consultar pistas y sus horarios</h2>
            <div id="leyenda">
                <span><span class="color" style="background:#28a745;"></span> Disponible</span>
                <span><span class="color" style="background:#dc3545;"></span> Ocupado</span>
            </div>
            <div class="accordion accordion-flush" id="elegirPista">
            <?php
                $crud = new Crud(new DB("proyecto"));
                $contador = 0;
                // Obtenemos todas las localizaciones y las añadimos al acordeón
                $localizaciones = $crud->listar("localizacion", "pistas", "group by localizacion");
                foreach($localizaciones as $localizacion){
            ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo "#flush-collapse$contador"; ?>" aria-expanded="false" aria-controls="<?php echo "flush-collapse$contador"; ?>">
                                <?php echo "$localizacion[localizacion]"; ?>
                            </button>
                        </h2>
                        <div id="<?php echo "flush-collapse$contador"; ?>" class="accordion-collapse collapse" data-bs-parent="#elegirPista">
                    <?php
                        // Para cada localización, añadimos las pistas al acordeón
                        $pistas = $crud->listar("nombre, id", "pistas", "where localizacion = \"$localizacion[localizacion]\"");
                        foreach($pistas as $pista){
                    ?>
                            <div class="accordion-body">
                                <input name="id" type="hidden" value=<?php echo "$pista[id]"; ?>>
                                <a class="nav-link ms-3 my-1"><?php echo "$pista[nombre]"; ?></a>
                            </div>
                    <?php
                        }
                    ?>
                        </div>
            <?php
                    $contador++;
                }
            ?>
                    </div>
                </div>
               
        </div>
        <h3 id="tituloPista" class="d-flex justify-content-center">Seleccione una pista</h3>
        <!-- Cerramos la sección principal, creada en navCliente.php -->
    </div>
    
    <div class="col" id="calendario">
        
    </div>
    </div>
<?php

    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
}
?>