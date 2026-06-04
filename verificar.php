<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . 'config.php';
    session_start();

    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/header.php";

    // Función para añadir scripts en la cabecera
    function añadirScriptsCabecera(){
?>
        <link rel="stylesheet" type="text/css" href="/css/estilosSubtitulo.css">
<?php }
?>
    <div class="card shadow-sm border-0 verificar">
        <div class="p-3 py-4">
            <div class="seccionSubtitulo mb-4">
<?php
    // Si hemos recibido el correo y el código del usuario
    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['codigo']) && !empty($_GET['codigo'])){
        $crud = new Crud(new DB("proyecto"));
	    $usuarioPendienteValidar = $crud->listar("email, codigo", "clientes", "where email = \"$_GET[email]\" and codigo = \"$_GET[codigo]\" and activo = 0");
        // Si tenemos una coincidencia, significa que hay que validar el usuario
        if($usuarioPendienteValidar != null){
            // Ponemos la columna "activo" del usuario a 1
            $crud->actualizar("clientes", "activo = 1", "where email = \"$_GET[email]\" and codigo = \"$_GET[codigo]\" and activo = 0");
?>
            <i class="ti ti-user-scan"></i>
                <div>
                    <h1>Has activado el usuario con email <?php echo $_GET['email'] ?></h1>
                    <small class="text-muted">Ahora puedes iniciar sesión dede <a href="/public/accesoCliente.php">aquí</a></small>
                </div>
<?php
        } 
        // Si no hay una coincidencia
        else {
?>
            <i class="ti ti-exclamation-circle"></i>
                <div>
                    <h1>La URL no es válida o la cuenta <?php echo $_GET['email'] ?> ya está activa</h1>
                    <small class="text-muted">Por favor, usa el enlace que se le ha enviado al email</small>
                </div>
<?php     
        }
    }
    // Si no se ha recibido el correo y el código del usuario, es porque no hemos accedido de la manera adecuada a esta página
    else {
?>
        <i class="ti ti-exclamation-circle"></i>
            <div>
                <h1>La URL no tiene un formato válido</h1>
                <small class="text-muted">Por favor, usa el enlace que se le ha enviado al email</small>
            </div>
<?php
    }
    echo "        </div>";
    echo "    </div>";
    echo "</div>";

    // Cargamos el pie
    require_once $_SERVER['DOCUMENT_ROOT'] . "/vista/template/footer.php";