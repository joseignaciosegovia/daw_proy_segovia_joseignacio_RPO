// Muestra un mensaje de error o de validación correcta para el "input" recibido

function showFeedBack(input, valid, message) {
    const validClass = (valid) ? 'is-valid' : 'is-invalid';
    const messageDiv = (valid) ? input.parentElement.querySelector('div.valid-feedback') : input.parentElement.querySelector('div.invalid-feedback');
    for (const div of input.parentElement.getElementsByTagName('div')) {
      div.classList.remove('d-block');
    }
    messageDiv.classList.remove('d-none');
    messageDiv.classList.add('d-block');
    input.classList.remove('is-valid');
    input.classList.remove('is-invalid');
    input.classList.add(validClass);
    if (message) {
      messageDiv.innerHTML = message;
    }
  }
  
  // Validación en línea por defecto
  
  function defaultCheckElement(event) {
    this.value = this.value.trim();
    if (!this.checkValidity()) {
      showFeedBack(this, false);
    } else {
      showFeedBack(this, true);
    }
  }
  
  // Validación del formulario con "submit" en línea
  
  function validacionJS() {
    const form = document.forms.crearUsuario;
  
    // Deshabilitamos la forma declarativa de validación
  
    form.setAttribute('novalidate', true);
  
    // Validación al enviar el formulario
  
    form.addEventListener('submit', function (event) {
      let isValid = true;
      let firstInvalidElement = null;
  
      const ncTelefono = document.getElementById('telefono');
  
      if (!ncTelefono.checkValidity()) {
        isValid = false;
        showFeedBack(ncTelefono, false);
  
        firstInvalidElement = ncTelefono;
      } else {
        showFeedBack(ncTelefono, true);
      }
  
      const ncConfirmar = document.getElementById('confirmarContraseña');
      const ncContraseña = document.getElementById('contraseña');
  
      if (ncConfirmar.value !== ncContraseña.value){
        isValid = false;
        showFeedBack(ncConfirmar, false, "Las contraseñas deben coincidir"); 
        firstInvalidElement = ncConfirmar;
      }
      else if (!ncConfirmar.checkValidity()) {
        isValid = false;
        if(ncConfirmar.validity.valueMissing) {
          showFeedBack(ncConfirmar, false, "Hay que confirmar la contraseña"); 
        }
        else {
            showFeedBack(ncConfirmar, false);
        }
  
        firstInvalidElement = ncConfirmar;
      } else {
        showFeedBack(ncConfirmar, true, "La confirmación de la contraseña es correcta");
      }
  
      if (!ncContraseña.checkValidity()) {
        isValid = false;
        if(ncContraseña.validity.valueMissing) {
          showFeedBack(ncContraseña, false, "Hay que introducir la contraseña"); 
        }
        else {
          showFeedBack(ncContraseña, false);
        }
  
        firstInvalidElement = ncContraseña;
      } else {
        showFeedBack(ncContraseña, true, "La contraseña es correcta");
      }
  
      const ncEmail = document.getElementById('email');
  
      if (!ncEmail.checkValidity()) {
        isValid = false;
        if(ncEmail.validity.valueMissing) {
          showFeedBack(ncEmail, false, "Hay que introducir el email"); 
        }
        else {
          showFeedBack(ncEmail, false);
        }
  
        firstInvalidElement = ncEmail;
      } else {
        showFeedBack(ncEmail, true, "El email es correcto");
      }
  
      const ncNombre = document.getElementById('nombre');
  
      if (!ncNombre.checkValidity()) {
        isValid = false;
        if(ncNombre.validity.valueMissing) {
          showFeedBack(ncNombre, false, "Hay que introducir el nombre"); 
        }
        else {
          showFeedBack(ncNombre, false);
        }
  
        firstInvalidElement = ncNombre;
      } else {
        showFeedBack(ncNombre, true, "El nombre es correcto");
      }
  
      if (!isValid) {
  
        // Indicamos que no se ha podido añadir el usuario
  
        mostrarModal("Error. El usuario " + ncNombre.value + " no ha podido crearse");
  
        // Ponemos el foco en el primer elemento incorrecto
  
        firstInvalidElement.focus();
      } else {

        let datosAEnviar = JSON.stringify({ 
          email: ncEmail.value, 
          nombre: ncNombre.value, 
          contraseña: ncContraseña.value, 
          telefono: ncTelefono.value
        });
  
        // Realizamos el envío al servidor
  
        const formData = new FormData();
  
        // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
        // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"
  
        formData.append("datos", datosAEnviar);
  
        // Invocamos el método en el que se añadirá una fila a la tabla con los datos del formulario
  
        fetch('index.php', {
          method: 'post',
          body: formData
        }).then((response) => response.text())
        .then(function(data) {
          mostrarModal("El usuario ", data + " ha sido creado correctamente");
          
        }).catch(function (err) {
          console.log("Ha habido un error");
        });
        
        // Disparamos el evento "reset" para resetear el formulario
  
        form.dispatchEvent(new Event('reset'));
      }
  
      // Prevenimos el comportamiento por defecto y la propagación
  
      event.preventDefault();
      event.stopPropagation();
  
    })

    // Reset del formulario

  form.addEventListener('reset', function (event) {
    for (const div of this.querySelectorAll('div.valid-feedback, div.invalid-feedback')) {
      div.classList.remove('d-block');
      div.classList.add('d-none');
    }

    for (const input of this.querySelectorAll('input')) {
      input.classList.remove('is-valid');
      input.classList.remove('is-invalid');
    }

    // Reseteamos el formulario

    form.reset();

    // Ponemos el foco en el primer elemento

    const ncNombre = document.getElementById('nombre');
    ncNombre.focus();
  })

    const ncTelefono = document.getElementById('telefono');
    const ncConfirmar = document.getElementById('confirmarContraseña');
    const ncContraseña = document.getElementById('contraseña');
    const ncEmail = document.getElementById('email');
    const ncNombre = document.getElementById('nombre');

  // Validación en línea de cada "input"

  ncTelefono.addEventListener('change', function (event) {
    if (!ncTelefono.checkValidity()) {
      showFeedBack(ncTelefono, false, "Introduzca un teléfono válido");
    } else {
      showFeedBack(ncTelefono, true);
    }
  });

  ncConfirmar.addEventListener('change', function (event) {
    if (ncConfirmar.value !== ncContraseña.value){
      showFeedBack(ncConfirmar, false, "Las contraseñas deben coincidir"); 
    }
    else if (!ncConfirmar.checkValidity()) {
      if(ncConfirmar.validity.valueMissing) {
        showFeedBack(ncConfirmar, false, "Hay que confirmar la contraseña"); 
      }
      else {
        showFeedBack(ncConfirmar, false);
      }
    } else {
      showFeedBack(ncConfirmar, true);
    }
  });

  ncContraseña.addEventListener('change', function (event) {
    if (ncConfirmar.value !== ncContraseña.value){
        showFeedBack(ncConfirmar, false, "Las contraseñas deben coincidir"); 
    }
    if (!ncContraseña.checkValidity()) {
      if(ncContraseña.validity.valueMissing) {
        showFeedBack(ncContraseña, false, "Hay que introducir la contraseña"); 
      }
      else {
        showFeedBack(ncContraseña, false);
      }
    } else {
      showFeedBack(ncContraseña, true);
    }
  });

  ncEmail.addEventListener('change', function (event) {
    if (!ncEmail.checkValidity()) {
      if(ncEmail.validity.valueMissing) {
        showFeedBack(ncEmail, false, "Hay que introducir el email"); 
      }
      else {
        showFeedBack(ncEmail, false);
      }
    } else {
      showFeedBack(ncEmail, true);
    }
  });

  ncNombre.addEventListener('change', function (event) {
    if (!ncNombre.checkValidity()) {
      if(ncNombre.validity.valueMissing) {
        showFeedBack(ncNombre, false, "Hay que introducir el nombre"); 
      }
      else {
        showFeedBack(ncNombre, false);
      }
    } else {
      showFeedBack(ncNombre, true);
    }
  });
  
}

function mostrarModal(titulo, texto) {
    // Obtenemos el contenedor principal del modal
  
    const modalContenedor = document.getElementById('modal');
  
    // Caambiamos el título del modal
  
    const modalTitulo = document.getElementById('modalLabel');
    modalTitulo.innerText = titulo;
  
    // Cambiamos el cuerpo del modal
  
    const modalCuerpo = modalContenedor.getElementsByClassName('modal-body')[0];
    modalCuerpo.replaceChildren();
    modalCuerpo.insertAdjacentHTML('afterbegin', `<div class="p-3">${texto}</div>`);
    
    // Creamos un objeto "Modal" para mostrar el modal recién modificado
  
    const modal = new bootstrap.Modal('#modal');
    modal.show();
  
    // Cuando pulsamos el botón "Close" hay que cerrar el modal
  
    const botonCerrar = $('.modal-footer .btn-secondary');
    $(botonCerrar[0]).on('click', function(event) {
  
      // Obtenemos el modal y lo ocultamos
  
      const modal = new bootstrap.Modal('#modal');
      modal.hide();
  
      event.stopPropagation();
    });
  }

export { validacionJS };