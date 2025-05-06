<?php
    // Cargamos la cabecera
    require_once "vista/template/header.php";
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h2>Reservar pistas del polideportivo</h2>
                <p>En esta página podrás registrarte para reservar las pistas del polideportivo y de la Ciudad Deportiva de Moral de Calatrava</p>
            </div>
            <div class="col">
                <form class="row g-3">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Correo electrónico" required>
                        <div class="invalid-feedback">
                            Introduzca un correo electrónico válido
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="Contraseña" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="Contraseña" placeholder="Contraseña" required>
                        <div id="passwordHelpBlock" class="form-text">
                            La contraseña debe cumplir los siguientes requisitos: 
                        </div>
                        <div class="invalid-feedback">
                            Introduzca una contraseña válida
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ConfirmarContraseña" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="ConfirmarContraseña" placeholder="Contraseña" required>
                        <div class="invalid-feedback">
                            Confirme la contraseña
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre Completo" required>
                        <div class="invalid-feedback">
                            Introduzca un nombre
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="number" class="form-control" id="telefono" placeholder="Teléfono (opcional)">
                        <div class="invalid-feedback">
                            Introduzca un número de teléfono válido
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </form>
            </div>
        </div>
    </div>
    

    <?php
        // Cargamos el pie
        require_once "vista/template/footer.php";
    ?>
    <!-- <script type="module" src="js/main.js"></script> -->