import {
    manejadores,
    botonComprar,
    handle_añadirCarrito,
    handle_eliminarCesta,
    handle_cambiarCantidad
} from './funciones.js';

window.addEventListener('load', function() {
    botonComprar();
    manejadores();
    const seccionUsuario = document.getElementById('usuario');
    cesta.style.display = "none";
    seccionUsuario.style.display = "none";
    eventos();
});

function eventos() {

    // Botón para eliminar artículos del carrito
    let eliminarCesta_btns = document.querySelectorAll(".eliminar-cesta");

    eliminarCesta_btns.forEach((btn) => {
        btn.addEventListener("click", handle_eliminarCesta);
    });

    // Botón para cambiar la cantidad de artículos del carrito
    let cantidadCesta_inputs = document.querySelectorAll(".cantidad-cesta");

    cantidadCesta_inputs.forEach((input) => {
        input.addEventListener("change", handle_cambiarCantidad);
    });

    // Botón para añadir artículos al carrito
    let añadirCarrito = document.querySelectorAll(".add-cart");

    añadirCarrito.forEach((boton) => {
        boton.addEventListener("click", handle_añadirCarrito);
    });
}