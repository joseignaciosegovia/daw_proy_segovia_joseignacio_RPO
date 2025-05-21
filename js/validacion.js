window.addEventListener('load', function() {
  const form = document.forms[0];

  switch(form.name) {
    case "crearUsuario":
      validacionCrearUsuario(form);
      break;
    case "perfilCliente":
      validacionPerfilCliente(form);
      break; 
    case "enviarIncidencias":
      validarEnviarIncidencias(form);
      break;  
    case "editarPista":
      validarEditarPista(form);
      break; 
    case "añadirPista":
      validarAñadirPista(form);
      break;
  }
});

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
  
// Validación del formulario para crear un usuario con "submit" en línea
function validacionCrearUsuario(form) {
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
        else if (ncContraseña.validitypatternMismatch) {
          showFeedBack(ncContraseña, false, "La contraseña debe tener al menos 8 caracteres"); 
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
          confirmarContraseña: ncConfirmar.value,
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
          mostrarModal("El usuario ha sido creado correctamente", "http://localhost/proyecto/index.php");

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
    else {
      showFeedBack(ncConfirmar, true);
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

// Validación del formulario para modificar el perfil del cliente
function validacionPerfilCliente(form) {
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

      // Indicamos que no se ha podido modificar el usuario

      mostrarModal("Error. El usuario " + ncNombre.value + " no ha podido modificarse");

      // Ponemos el foco en el primer elemento incorrecto

      firstInvalidElement.focus();
    } else {

      let datosAEnviar = JSON.stringify({ 
        nombre: ncNombre.value, 
        contraseña: ncContraseña.value, 
        confirmarContraseña: ncConfirmar.value,
        telefono: ncTelefono.value
      });

      // Realizamos el envío al servidor

      const formData = new FormData();

      // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
      // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

      formData.append("datos", datosAEnviar);

      // Invocamos el método en el que se añadirá una fila a la tabla con los datos del formulario

      fetch('perfilCliente.php', {
        method: 'post',
        body: formData
      }).then((response) => response.text())
      .then(function(data) {
        mostrarModal("El usuario ha sido modificado correctamente", "http://localhost/proyecto/public/perfilCliente.php");
        
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
    else {
      showFeedBack(ncConfirmar, true);
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

// Validación del formulario para enviar incidencias/sugerencias
function validarEnviarIncidencias(form) {
  // Deshabilitamos la forma declarativa de validación
  
  form.setAttribute('novalidate', true);

  // Validación al enviar el formulario

  form.addEventListener('submit', function (event) {
    let isValid = true;
    let firstInvalidElement = null;

    const ncIncidencia = document.getElementById('quejaIncidencia');

    if (!ncIncidencia.checkValidity()) {
      isValid = false;
      showFeedBack(ncIncidencia, false);

      firstInvalidElement = ncIncidencia;
    } else {
      showFeedBack(ncIncidencia, true);
    }

    if (!isValid) {

      // Indicamos que no se ha podido enviar la incidencia/queja

      mostrarModal("Error. la incidencia no ha podido enviarse");

      // Ponemos el foco en el primer elemento incorrecto

      firstInvalidElement.focus();
    } else {

      let datosAEnviar = JSON.stringify({ 
        contenido: ncIncidencia.value
      });

      // Realizamos el envío al servidor

      const formData = new FormData();

      // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
      // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

      formData.append("datos", datosAEnviar);

      // Invocamos el método en el que se añadirá una fila a la tabla con los datos del formulario

      fetch('incidenciasQuejas.php', {
        method: 'post',
        body: formData
      }).then((response) => response.text())
      .then(function(data) {
        mostrarModal("La incidencia ha sido enviada correctamente", "http://localhost/proyecto/public/incidenciasQuejas.php");
        
      }).catch(function (err) {
        mostrarModal("Ha habido un error");
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

    const ncIncidencia = document.getElementById('quejaIncidencia');
    ncIncidencia.focus();
  })

  // Validación en línea de cada "input"

  const ncIncidencia = document.getElementById('quejaIncidencia');

  ncIncidencia.addEventListener('change', function (event) {
    if (!ncIncidencia.checkValidity()) {
      showFeedBack(ncIncidencia, false, "Introduzca un mensaje válido");
    } else {
      showFeedBack(ncIncidencia, true);
    }
  });
}

// Validación del formulario para editar una pista
function validarEditarPista(form) {
  // Deshabilitamos la forma declarativa de validación
  
  form.setAttribute('novalidate', true);

  // Validación al enviar el formulario

  form.addEventListener('submit', function (event) {
    let isValid = true;
    let firstInvalidElement = null;

    const ncPrecio = document.getElementById('precio');

    if (!ncPrecio.checkValidity()) {
      isValid = false;
      showFeedBack(ncPrecio, false);

      firstInvalidElement = ncPrecio;
    } else {
      showFeedBack(ncPrecio, true);
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

      // Indicamos que no se ha podido modificar la pista

      mostrarModal("Error. La pista " + ncNombre.value + " no ha podido modificarse");

      // Ponemos el foco en el primer elemento incorrecto

      firstInvalidElement.focus();
    } else {
      const ncLocalizacion = document.getElementById('Localizacion');
      const ncNombreOriginal = document.getElementById('nombreOriginal');

      let datosAEnviar = JSON.stringify({ 
        nombre: ncNombre.value, 
        localizacion: [...ncLocalizacion.selectedOptions].map((option) => option.value), 
        precio: ncPrecio.value,
        nombreOriginal: ncNombreOriginal.value
      });

      // Realizamos el envío al servidor

      const formData = new FormData();

      // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
      // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

      formData.append("datos", datosAEnviar);

      // Invocamos el método en el que se añadirá una fila a la tabla con los datos del formulario

      fetch('editarPista.php', {
        method: 'post',
        body: formData
      }).then((response) => response.text())
      .then(function(data) {
        mostrarModal("La pista ha sido modificada correctamente", "http://localhost/proyecto/servidor/intranet.php");
      }).catch(function (err) {
        mostrarModal("Ha habido un error");
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

  const ncPrecio = document.getElementById('precio');
  const ncNombre = document.getElementById('nombre');

  // Validación en línea de cada "input"

  ncPrecio.addEventListener('change', function (event) {
    if (!ncPrecio.checkValidity()) {
      showFeedBack(ncPrecio, false, "Introduzca un precio válido");
    } else {
      showFeedBack(ncPrecio, true);
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

// Validación del formulario para añadir una pista
function validarAñadirPista(form) {
  // Deshabilitamos la forma declarativa de validación
  
  form.setAttribute('novalidate', true);

  // Validación al enviar el formulario

  form.addEventListener('submit', function (event) {
    let isValid = true;
    let firstInvalidElement = null;

    const ncPrecio = document.getElementById('precio');

    if (!ncPrecio.checkValidity()) {
      isValid = false;
      showFeedBack(ncPrecio, false);

      firstInvalidElement = ncPrecio;
    } else {
      showFeedBack(ncPrecio, true);
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

      // Indicamos que no se ha podido añadir la pista

      mostrarModal("Error. La pista " + ncNombre.value + " no ha podido añadirse");

      // Ponemos el foco en el primer elemento incorrecto

      firstInvalidElement.focus();
    } else {
      const ncLocalizacion = document.getElementById('Localizacion');

      let datosAEnviar = JSON.stringify({ 
        nombre: ncNombre.value, 
        localizacion: [...ncLocalizacion.selectedOptions].map((option) => option.value), 
        precio: ncPrecio.value
      });

      // Realizamos el envío al servidor

      const formData = new FormData();

      // Al llamar "datos" al parámetro del "formData" que enviamos al servidor,
      // éste accederá a su contenido (es decir, "datosAEnviar") con "$_POST['datos']"

      formData.append("datos", datosAEnviar);

      // Invocamos el método en el que se añadirá una fila a la tabla con los datos del formulario

      fetch('añadirPista.php', {
        method: 'post',
        body: formData
      }).then((response) => response.text())
      .then(function(data) {
        mostrarModal("La pista ha sido añadida correctamente", "http://localhost/proyecto/servidor/intranet.php");
        
      }).catch(function (err) {
        mostrarModal("Ha habido un error");
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

  const ncPrecio = document.getElementById('precio');
  const ncNombre = document.getElementById('nombre');

  // Validación en línea de cada "input"

  ncPrecio.addEventListener('change', function (event) {
    if (!ncPrecio.checkValidity()) {
      showFeedBack(ncPrecio, false, "Introduzca un precio válido");
    } else {
      showFeedBack(ncPrecio, true);
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

function mostrarModal(titulo, direccion, texto) {
    // Obtenemos el contenedor principal del modal
  
    const modalContenedor = document.getElementById('modal');
  
    // Cambiamos el título del modal
  
    const modalTitulo = document.getElementById('modalLabel');
    modalTitulo.innerText = titulo;
  
    // Cambiamos el cuerpo del modal
  
    const modalCuerpo = modalContenedor.getElementsByClassName('modal-body')[0];
    modalCuerpo.replaceChildren();

    if(texto != null) {
      modalCuerpo.insertAdjacentHTML('afterbegin', `<div class="p-3">${texto}</div>`);
    }
    
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

      if(direccion != null) {
        // Nos situamos en la dirección indicada
        location.replace(direccion);
      }
    });
}

export { validacionCrearUsuario as validacionJS };