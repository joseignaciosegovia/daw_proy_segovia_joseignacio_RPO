let productosCarrito = [];

// Actualizar carrito
function update() {
    eventos();
    updateTotal();
}

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
    let añadirCarrito = document.querySelectorAll(".añadir-carrito");

    añadirCarrito.forEach((boton) => {
        boton.addEventListener("click", handle_añadirCarrito);
    });

    // Botón para añadir artículos a la lista de deseos
    let añadirDeseos = document.querySelectorAll(".añadir-deseos");

    añadirDeseos.forEach((boton) => {
        boton.addEventListener("click", handle_añadirDeseos);
    });
}

// Cargamos las novedades
async function cargarNovedades() {
    // Ocultamos el resto de secciones y mostramos la sección de novedades
    ofertas.style.display = "none";
    ordenadores.style.display = "none";
    componentes.style.display = "none";
    novedades.style.display = "block";
    
    novedades.replaceChildren();
    novedades.insertAdjacentHTML('beforeend', `<h2>Novedades</h2>`);

    const formData = new FormData();
    formData.append("data", "novedades");

    try {
        const respuesta = await fetch('servidor/devolverProductos.php', {
            method: 'post',
            body: formData
          });
        const datos = await respuesta.json();
        mostrarProductos(novedades, datos);
    } catch(error) {
        console.log("Error: " + error);
    }

    eventos();
}

// Cargamos las ofertas
async function cargarOfertas() {
    // Ocultamos el resto de secciones y mostramos la sección de ofertas
    ofertas.style.display = "block";
    ordenadores.style.display = "none";
    componentes.style.display = "none";
    novedades.style.display = "none";

    ofertas.replaceChildren();
    ofertas.insertAdjacentHTML('beforeend', `<h2>Ofertas</h2>`);

    const formData = new FormData();
    formData.append("data", "ofertas");

    try {
        const respuesta = await fetch('servidor/devolverProductos.php', {
            method: 'post',
            body: formData
          });
        const datos = await respuesta.json();
        mostrarProductos(ofertas, datos);
    } catch(error) {
        console.log("Error: " + error);
    }

    eventos();
}

// Cargamos los ordenadores
async function cargarOrdenadores() {
    // Ocultamos el resto de secciones y mostramos la sección de ordenadores
    ofertas.style.display = "none";
    ordenadores.style.display = "block";
    componentes.style.display = "none";
    novedades.style.display = "none";
    
    ordenadores.replaceChildren();
    ordenadores.insertAdjacentHTML('beforeend', `<h2>Ordenadores</h2>`);

    const formData = new FormData();
    formData.append("data", "ordenadores");

    try {
        const respuesta = await fetch('servidor/devolverProductos.php', {
            method: 'post',
            body: formData
          });
        const datos = await respuesta.json();
        mostrarProductos(ordenadores, datos);
    } catch(error) {
        console.log("Error: " + error);
    }

    eventos();

}

// Cargamos los componentes
async function cargarComponentes() {
    // Ocultamos el resto de secciones y mostramos la sección de novedades
    ofertas.style.display = "none";
    ordenadores.style.display = "none";
    componentes.style.display = "block";
    novedades.style.display = "none";

    componentes.replaceChildren();
    componentes.insertAdjacentHTML('beforeend', `<h2>Componentes</h2>`);

    const formData = new FormData();
    formData.append("data", "componentes");

    try {
        const respuesta = await fetch('servidor/devolverProductos.php', {
            method: 'post',
            body: formData
          });
        const datos = await respuesta.json();
        mostrarProductos(componentes, datos);
    } catch(error) {
        console.log("Error: " + error);
    }

      eventos();
}

function manejadores() {
    const carritoIcono = document.querySelector('.bi-cart-dash');
    const cesta = document.getElementById('cesta');

    const lupaIcono = document.querySelector('.bi-search');
    const usuarioIcono = document.querySelector('.bi-person-circle');
    const seccionUsuario = document.getElementById('usuario');
    const cerrarCesta = document.querySelector('#cerrar-cesta');

    // Pulsamos en el icono del carrito
    carritoIcono.addEventListener("click", () => {
        cesta.style.display = "block";
    });

    // Pulsamos en el icono del usuario
    usuarioIcono.addEventListener("click", () => {

        // Comprobamos si hay un usuario logeado

        fetch('devolverCliente.php', {
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

    // Pulsamos el botón de cerrar en el menú de la cesta
    cerrarCesta.addEventListener("click", () => {
        cesta.style.display = "none";
    });

    // Pulsamos el botón de cerrar en el menú del usuario
    seccionUsuario.addEventListener("click", () => {
        seccionUsuario.style.display = "none";
    });
}

// Comportamiento de botones
function botonComprar() {
    const comprar_btn = document.querySelector(".btn-comprar");
    comprar_btn.addEventListener("click", handle_buyOrden);
}

function cestaBoxComponent(nombre, precio, rutaImagen) {
    return `
    <div class="cesta-box">
        <img src="${rutaImagen}" alt="" class="imagen-cesta">
        <div class="detail-box">
        <div class="nombre-producto-cesta">${nombre}</div>
        <div class="precio-cesta">${precio}€</div>
        <input type="number" value="1" class="cantidad-cesta">
    </div>

    <!-- ELIMINAR cesta -->
    <i class="bi bi-trash-fill eliminar-cesta"></i>
    `;
}

// Manejador del botón para añadir artículos al carrito
function handle_añadirCarrito() {
    let producto = this.parentElement;
    let nombre = producto.querySelectorAll('p')[0].innerHTML;
    let precioTexto = producto.querySelectorAll('p')[1].textContent;
    // Eliminar símbolos de moneda y separadores de miles
    let precio = parseFloat(precioTexto.replace("€", "").replace(".", "")); 

    let rutaImagen = producto.querySelector('img').src;
    
    let productoAñadir = {
        nombre: nombre,
        precio: precio,
        rutaImagen: rutaImagen
    };
    
    if(productosCarrito.find((productoExistente) => productoExistente.nombre === productoAñadir.nombre)){
        alert("Este Articulo Ya Existe");
    } else {
        productosCarrito.push(productoAñadir);
    }
    
    // Añadir productos al carrito

    let carBoxElemento = cestaBoxComponent(nombre, precio, rutaImagen);
    let nuevoNodo = document.createElement("div");
    nuevoNodo.innerHTML = carBoxElemento;
    const contenidoCesta = cesta.querySelector(".contenido-cesta");
    contenidoCesta.appendChild(nuevoNodo);

    // Activar el icono del carrito
    cesta.style.display = "block";

    update()
}

// Manejador del botón para añadir artículos a la lista de deseos
function handle_añadirDeseos() {

    const formData = new FormData();

    const datos = ["añadir", this.parentNode.querySelector('a').innerText, this.parentNode.querySelectorAll('p')[1].innerText];

    formData.append("datos", JSON.stringify(datos));

    fetch('servidor/actualizarListaDeseos.php', {
        method: 'post',
        body: formData
      }).then((response) => response.text())
      .then(function(data) {
        alert(data);
      }).catch(function(data) {
        console.log("Error");
      }
    );
}

function handle_eliminarCesta() {
    this.parentElement.remove();

    productosCarrito = productosCarrito.filter(
        (el) => el.title !== this.parentElement.querySelector(".nombre-producto-cesta").innerHTML
    );    
    update();
}

// Pulsamos el botón 'Cerrar sesión'
function handle_cerrarClientes() {
    fetch('/proyecto/public/cerrar.php', {
        method: 'get'
      }).then((response) => response.text())
      .then(function(data) {
        window.open("/proyecto/index.php", "_self");
      }).catch(function(data) {
        console.log("Error");
      }
    );
}

function handle_cambiarCantidad(){
    if(isNaN(this.value) || this.value < 1){
        this.value = 1;
    }

    // Para mantener el número entero
    this.value = Math.floor(this.value); 

    update();
}

function handle_buyOrden() {
    // Si se intenta comprar antes de hacer un pedido
    if(productosCarrito.length <= 0){
        alert("No tiene ningún producto en la cesta");
        return;
    }
    const contenidoCesta = cesta.querySelector(".contenido-cesta");
    contenidoCesta.innerHTML = "";
    alert("Su pedido se realizó exitosamente")

    const formData = new FormData();
    formData.append("productos", JSON.stringify(productosCarrito));

    fetch('public/comprar.php', {
        method: 'post',
        body: formData
      }).then((response) => response.text())
      .then(function(data) {

      }).catch(function(data) {
        console.log("Error");
      }
    );

    productosCarrito = [];
    update();
}

// Pulsamos el botón 'Acceso a trabajadores'
function handle_trabajadores() {
    // Abrimos una pestaña con el acceso a los trabajadores
    window.open("/proyecto/servidor/servidor.php", "Intranet");
}

// Pulsamos el botón 'Acceso a Usuarios'
function handle_clientes() {
    // Abrimos en la misma pestaña el acceso a los usuarios
    window.open("/proyecto/public/accesoCliente.php", "_self");
}

// Pulsamos el botón 'Registrarse'
function handle_registroClientes() {
    // Abrimos en la misma pestaña el registro de los clientes
    window.open("/proyecto/public/registroCliente.php", "_self");
}

// Actualizar y renderizar
function updateTotal() {
    let cestaBoxes = document.querySelectorAll('.cesta-box');
    const totalElement = cesta.querySelector(".precio-total");
    let total=0;

    cestaBoxes.forEach((cestaBox) => {
        let precioElemento  = cestaBox.querySelector(".precio-cesta");
        let precio = parseFloat(precioElemento.innerHTML.replace("€", ""));
        let cantidad = cestaBox.querySelector(".cantidad-cesta").value;

        total += precio * cantidad;
    });

    total = total.toFixed(2);

    totalElement.innerHTML = total + "€";
}

function mostrarProductos(productos, datos) {
    productos.insertAdjacentHTML('beforeend', `<div class="divProductos"></div>`);
    let divProductos = productos.childNodes[1];
    divProductos.replaceChildren();
    for(let i = 0; i < 6 && i < datos.length; i++) {
        divProductos.insertAdjacentHTML('beforeend', `<div class="productos">
            <img class="imagenProducto" src="img/${datos[i]['imagen']}"></img>
            <a href="public/productos.php?producto=${datos[i]['nombre']}"><p>${datos[i]['nombre']}</p></a>
            <p>${datos[i]['precio_actual']}€</p>
        </div>`);
        if(datos[i]['precio_actual'] != datos[i]['precio_original']){
            divProductos.childNodes[i].insertAdjacentHTML('beforeend', `<strike>${datos[i]['precio_original']}€</strike>
            `);
        }
        // Añadimos los iconos de añadir a la lista de deseos y de añadir al carrito
        divProductos.childNodes[i].insertAdjacentHTML('beforeend', `<i class="bi bi-heart añadir-deseos"></i>
            <i class="bi bi-bag-dash-fill añadir-carrito"></i>
        `);
    }
}

export{
    update,
    cargarNovedades,
    cargarOfertas,
    cargarOrdenadores,
    cargarComponentes,
    manejadores,
    cestaBoxComponent,
    handle_añadirCarrito,
    handle_añadirDeseos,
    handle_eliminarCesta,
    handle_cerrarClientes,
    handle_cambiarCantidad,
    handle_buyOrden,
    handle_trabajadores,
    handle_clientes,
    handle_registroClientes,
    updateTotal,
    botonComprar,
    mostrarProductos,
};