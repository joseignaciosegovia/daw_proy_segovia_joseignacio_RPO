<?php
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

    // Actualizamos el título de la página
    $titulo = "Administración de gestores · Moral de Calatrava";
    // Actualizamos la dirección del título y del logo de la página
    $home = "/servidor/intranet.php";

    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";
    use Clases\DB;

    // Si hemos iniciado sesión como gestor y el gestor es administrador
    if(!empty($_SESSION["gestor"]) && !empty($_SESSION["administrador"])){
        $crud = new Crud(new DB("proyecto"));
        $gestores = $crud->listar("*", "gestores", "");
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
                        <h2>Lista de Gestores</h2>
                        <small class="text-muted">Consulta y modifica los datos de los gestores</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Correo</th>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th>Teléfono</th>
                                <th>¿Es administrador?</th>
                                <th>Editar gestor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cont = 1;
                                // Recorremos los gestores y los añadimos a la tabla
                                foreach($gestores as $gestor){
                            ?>
                            <tr>
                                <th><?php echo $cont ?></th>
                                <td><?php echo $gestor['email'] ?></td>
                                <td><?php echo $gestor['nombre'] ?></td>
                                <td><?php echo $gestor['DNI'] ?></td>
                                <td><?php echo $gestor['telefono'] ?></td>
                                <td><?php 
                                    // Si el gestor es también administrador lo indicamos
                                    if($gestor['administrador'] == 1)
                                        echo "Sí";
                                    else
                                        echo "No";
                                ?>
                                </td>
                                <td><?php echo "<button class=\"btn btn-warning form-floating\" onclick=\"window.location.href='editarGestor.php?gestor=$gestor[email]';\">Editar</button>"?></td>
                            </tr>
                                <?php 
                                    $cont++;
                                } 
                                ?>
                        </tbody>
                    </table>
                </div>
                <form method='POST' action='<?php echo "añadirGestor.php"; ?>'>
                    <input type="submit" class="btn btn-success" name="Añadir" value="Añadir gestor">
                </form>
            </div>
        </div>
    <?php } ?>
    </main>
    <!-- Cerramos la sección principal, creada en navGestor.php -->
</div>
<?php
    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";
?>