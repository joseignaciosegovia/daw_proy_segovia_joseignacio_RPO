<?php
    // Cargamos la cabecera
    require_once "vista/template/header.php";
?>
        <div id="usuario">
            <h2 class="usuario-titulo">Usuario</h2>
            <button type="button" class="btn-trabajadores">Acceso a Administradores</button>
            <button type="button" class="btn-usu">Acceso a Clientes</button>
            <button type="button" class="btn-login">Registrarse</button>
            <i class="bi bi-x-circle" id="cerrarUsuario"></i>
        </div>

        </div>

        <?php
            // Cargamos el pie
            require_once "vista/template/footer.php";
        ?>
       <script type="module" src="js/main.js"></script>