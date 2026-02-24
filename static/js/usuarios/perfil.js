import {GeneralEventListener} from '../utils/listeners.js';
import { toggleLoading } from '../utils/ui.js';

GeneralEventListener('input-avatar', 'change', cambiarFotoPerfil);
GeneralEventListener('btn-guardar-datos', 'click', guardarDatosGenerales);
GeneralEventListener('btn-cambiar-pass', 'click', cambiarContraseña);


function cambiarFotoPerfil() {
    const file = this.files[0];
    if (!file) return;

    const maxMB = 2;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    // 1. Validar Tipo
    if (!allowedTypes.includes(file.type)) {
        Toast.fire({ icon: "error", title: "Solo se permiten imágenes (JPG, PNG, WEBP)" });
        this.value = ""; // Limpiar el input
        return;
    }
    // 2. Validar Tamaño (file.size está en bytes)
    if (file.size > maxMB * 1024 * 1024) {
        Toast.fire({ icon: "error", title: `La imagen es muy pesada. Máximo ${maxMB}MB` });
        this.value = "";
        return;
    }

    const idBtn = 'btn-cambiar-foto';
    const avatarPreview = document.getElementById('avatar-preview');
    const fotoNavbar = document.getElementById('foto_usuario_navbar');
    const originalSrc = avatarPreview.src;
    const avatarLoader = document.getElementById('avatar-loader'); // El nuevo div

    // 1. Activar loaders y opacidad en ambas fotos
    toggleLoading(idBtn, true, "Subiendo...");
    avatarPreview.style.opacity = '0.5';
    avatarLoader.classList.remove('d-none');

    if (fotoNavbar) fotoNavbar.style.opacity = '0.3'; // El navbar también "carga"

    const formData = new FormData();
    formData.append('avatar', file);

    fetch(BASE_URL + 'api/usuarios.php?tipo=actualizar_foto_perfil', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.estatus === true) {
            const timestamp = new Date().getTime();
            const nuevaRuta = STATIC_URL + `img/avatars/${data.filename}?t=${timestamp}`;
            
            // 2. Actualizar las fuentes
            avatarPreview.src = nuevaRuta;
            if (fotoNavbar) fotoNavbar.src = nuevaRuta;

            // 3. Pequeño retraso de 700ms para que se aprecie el cambio
            setTimeout(() => {
                Toast.fire({
                    icon: "success",
                    title: data.mensaje,
                });
                
                // Restaurar UI después del delay
                toggleLoading(idBtn, false);
                avatarPreview.style.opacity = '1';
                if (fotoNavbar) fotoNavbar.style.opacity = '1';
                avatarLoader.classList.add('d-none'); // Ocultar spinner
            }, 700);

        } else {
            Toast.fire({ icon: "error", title: data.mensaje });
            avatarPreview.src = originalSrc;
            // Restaurar inmediatamente en caso de error
            toggleLoading(idBtn, false);
            avatarPreview.style.opacity = '1';
            if (fotoNavbar) fotoNavbar.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.fire({ icon: "error", title: "Error en la conexión" });
        avatarPreview.src = originalSrc;
        avatarLoader.classList.add('d-none');
        toggleLoading(idBtn, false);
        avatarPreview.style.opacity = '1';
        if (fotoNavbar) fotoNavbar.style.opacity = '1';
    });
    // Quitamos el .finally() para controlar el tiempo manualmente en el éxito
};

function guardarDatosGenerales(){
    const idBtn = 'btn-guardar-datos'
    const nombre = document.getElementById('nombre').value
    const apellido = document.getElementById('apellido').value
    const correo = document.getElementById('correo').value

    toggleLoading(idBtn, true, "Validando...");

    $.ajax({
        type: "post",
        url: BASE_URL + "api/usuarios.php?tipo=guardar_datos_generales",
        data: {nombre, apellido, correo},
        dataType: "json",
        success: function (response) {
            if (response.estatus === true) {
                Toast.fire({
                    icon: "success",
                    title: response.mensaje,
                  });
                  console.log(response.data.nombre);
                  document.getElementById('nombre_usuario_navbar').textContent = response.data.nombre + ' ' + response.data.apellido
            } else {
                Toast.fire({
                    icon: "error",
                    title:response.mensaje,
                  });
            }
        }, error: function() {
            Toast.fire({ icon: "error", title: "Error en el servidor" });
        },
        complete: function() {
            // 3. DESBLOQUEO: Regresar el botón a la normalidad al terminar (éxito o error)
            toggleLoading(idBtn, false);
        }
    });
}

const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  }) 

  document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        // Encontrar el input que está justo antes del botón
        const input = this.parentElement.querySelector('input');
        const icon = this.querySelector('svg'); // Feather usa SVG

        if (input.type === "password") {
            input.type = "text";
            // Cambiar icono a "ojo tachado" (eye-off)
            this.innerHTML = '<i class="align-middle" data-feather="eye-off"></i>';
        } else {
            input.type = "password";
            // Cambiar icono a "ojo" (eye)
            this.innerHTML = '<i class="align-middle" data-feather="eye"></i>';
        }
        
        // Re-inicializar Feather Icons para que el nuevo icono se renderice
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
});

const passNueva = document.getElementById('pass_nueva');
const passRepite = document.getElementById('pass_repite');
const passActual= document.getElementById('pass_actual')

function validarPassword() {
    if(!passActual.value){
        document.getElementById('btn-cambiar-pass').classList.add('disabled')
    }

    // Solo validamos si el segundo campo tiene algo escrito
    if (passRepite.value.length > 0) {
        if (passNueva.value === passRepite.value) {
            // Coinciden: Quitamos rojo y ponemos verde
            if(passActual.value){
                document.getElementById('btn-cambiar-pass').classList.remove('disabled')
            }

            passRepite.classList.remove('is-invalid');
            passRepite.classList.add('is-valid');
        } else {
            // No coinciden: Ponemos rojo
            document.getElementById('btn-cambiar-pass').classList.add('disabled')
            passRepite.classList.remove('is-valid');
            passRepite.classList.add('is-invalid');
        }
    } else {
        // Limpiar estilos si el campo está vacío
        document.getElementById('btn-cambiar-pass').classList.add('disabled')
        passRepite.classList.remove('is-invalid', 'is-valid');
    }
}

function cambiarContraseña(){

    if(!passActual.value) { Toast.fire({icon: "warning", title: 'Ingresa la contraseña actual'}); return false;}
    if(!passRepite.value)  {Toast.fire({icon: "warning", title: 'Ingresa la contraseña nueva'});return false;}
    if(!passNueva.value)  {Toast.fire({icon: "warning", title: 'Ingresa la repetición de la contraseña nueva'});return false;}
    
    const idBtn = 'btn-cambiar-pass';

    // 2. BLOQUEO: Deshabilitar botón y mostrar loading
    toggleLoading(idBtn, true, "Validando...");

    $.ajax({
        type: "post",
        url: BASE_URL + "api/usuarios.php?tipo=cambiar_contraseña",
        data: {pass_actual: passActual.value, pass_nueva: passNueva.value},
        dataType: "json",
        success: function (response) {
            if (response.estatus === true) {
                Toast.fire({
                    icon: "success",
                    title: response.mensaje,
                  });

                  passActual.value = "";
                passNueva.value = "";
                passRepite.value = "";

            } else {
                Toast.fire({
                    icon: "error",
                    title:response.mensaje,
                  });
            }
        },
        error: function() {
            Toast.fire({ icon: "error", title: "Error en el servidor" });
        },
        complete: function() {
            // 3. DESBLOQUEO: Regresar el botón a la normalidad al terminar (éxito o error)
            toggleLoading(idBtn, false);
        }
    });
}

// Ejecutar la función cada vez que el usuario escribe
passActual.addEventListener('input', validarPassword);
passNueva.addEventListener('input', validarPassword);
passRepite.addEventListener('input', validarPassword);