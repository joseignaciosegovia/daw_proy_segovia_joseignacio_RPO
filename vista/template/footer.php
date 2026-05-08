            <footer>
                <div class="container-fluid">
                    <div class="row">
                        <h2>Información de interés</h2>
                    </div>
                    <div class="row">
                        <div class="col" id="telefonos">
                            <h3>Teléfonos:</h3>
                            <ul class="nav flex-column">
                                <li class="nav-item mb-2">Ciudad deportiva: 656 539 016</li>
                                <li class="nav-item mb-2">Polideportivo: 926 319 495</li>
                            </ul>
                        </div>
                        <div class="col" id="correosElectronicos">
                            <h3>Correos electrónicos:</h3>
                            <ul class="nav flex-column">
                                <li class="nav-item mb-2">Ciudad deportiva: Ciudaddeportivamoral@gmail.com</li>
                            </ul>
                        </div>
                        <div class="col" id="paginaWeb">
                            <h3>Páginas web:</h3>
                            <ul class="nav flex-column">
                                <li><a href="https://www.moraldecalatrava.org/web1/ciudad-deportiva-moral-de-calatrava/" target="_blank">Ciudad Deportiva</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- JQuery -->
            <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <!-- Bootstrap -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
            <!-- Cada página añadirá los scripts que necesite -->
            <?php if (function_exists('añadirScriptsPie')){
                añadirScriptsPie();
            }?>
        </body>
</html>