<?php

    session_start();

    use Clases\Cliente;
    use Clases\DB;
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/controlador/Crud.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/modelo/Cliente.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/header.php";

    $crud = new Crud(new DB("proyecto"));
    $cliente = $crud->obtener("clientes", "email = \"$_SESSION[cliente]\"")[0];

    echo "<h2 class=\"d-flex justify-content-center py-2\">Bienvenido/a $cliente[nombre]</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto/vista/template/nav.php";

?>

        <div class="col">
            <h4>Escoger pista</h4>
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        Polideportivo
                    </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Pista interna" ?>">Pista interna</a>
                        </div>
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Pista externa" ?>">Pista externa</a>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        Ciudad Deportiva
                    </button>
                    </h2>
                    <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Campo fútbol 7" ?>">Campo fútbol 7</a>
                        </div>
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Campo fútbol 11" ?>">Campo fútbol 11</a>
                        </div>
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Pádel" ?>">Pádel</a>
                        </div>
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Atletismo" ?>">Atletismo</a>
                        </div>
                        <div class="accordion-body">
                            <a class="nav-link ms-3 my-1" href="<?php echo "$_SERVER[PHP_SELF]?pista=Multiusos" ?>">Multiusos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
    if (isset($_GET['pista'])) {
        $calendario = $crud->obtener("calendarios", "pista = \"$_GET[pista]\"");

        

        foreach($calendario as $fecha) {
            echo "Fecha " . $fecha['fechaOcupada'] . " y hora: " . $fecha['horaOcupada'];
        }
        
        
    }

?>    

<?php
    require_once "../vista/template/footer.php";
?>
