import {
    cargarNovedades,
    cargarOfertas,
    cargarOrdenadores,
    cargarComponentes,
    handle_cerrarClientes,
    handle_buyOrden,
    handle_trabajadores,
    handle_clientes,
    handle_registroClientes,
} from './funciones.js';

// Iconos
const carritoIcono = document.querySelector('.bi-cart-dash');
const cesta = document.getElementById('cesta');

const lupaIcono = document.querySelector('.bi-search');
const usuarioIcono = document.querySelector('.bi-person-circle');
const seccionUsuario = document.getElementById('usuario');
const cerrarCesta = document.querySelector('#cerrar-cesta');
const novedades = document.querySelector('#novedades');
const ofertas = document.querySelector('#ofertas');
const ordenadores = document.querySelector('#ordenadores');
const componentes = document.querySelector('#componentes');

// Botón para el acceso de los trabajadores
const btnTrabajadores = document.querySelector(".btn-trabajadores");
btnTrabajadores.addEventListener("click", handle_trabajadores);

// Botón para el acceso de los clientes
const btnClientes = document.querySelector(".btn-usu");
btnClientes.addEventListener("click", handle_clientes);

// Botón para el registro de los clientes
const btnRegistroClientes = document.querySelector(".btn-login");
btnRegistroClientes.addEventListener("click", handle_registroClientes);

// Productos del carrito
let productosCarrito = [];

// Pulsamos en el icono del carrito
carritoIcono.addEventListener("click", () => {
    cesta.style.display = "block";
});

// Pulsamos en el icono del usuario
usuarioIcono.addEventListener("click", () => {

    // Comprobamos si hay un usuario logeado

    fetch('/proyecto/public/devolverCliente.php', {
        method: 'get'
      }).then ((response) => response.json()
      ).then(function (data) {
        if(data){
            seccionUsuario.replaceChildren();
            seccionUsuario.insertAdjacentHTML('beforeend', `</br></br><a href="/proyecto/public/perfil.php">Perfil</a></br>
                <a href="/proyecto/public/historialCompras.php">Historial de compras</a></br>
                <a href="/proyecto/public/listaDeseos.php">Lista de deseos</a></br>
                <a href="/proyecto/public/quejas.php">Quejas y sugerencias</a></br>
                <i class="bi bi-x-circle" id="cerrarUsuario"></i>
                <button type="button" class="btn-salir">Cerrar sesión</button>`);
        }
            
        // Botón para cerrar sesión de los clientes
        const btnCerrarClientes = document.querySelector(".btn-salir");
        btnCerrarClientes.addEventListener("click", handle_cerrarClientes);
        seccionUsuario.style.display = "block";

        // FUNCIONA MAL!!!!!!!
      }).catch(function (err) {
        console.log("Ha habido un error");
        seccionUsuario.style.display = "block";
      });
});

// Pulsamos el botón de cerrar en el menú del usuario
seccionUsuario.addEventListener("click", () => {
    seccionUsuario.style.display = "none";
});

window.addEventListener('load', function() {
    seccionUsuario.style.display = "none";

    
});