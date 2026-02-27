import { GeneralEventListener } from '../utils/listeners.js';
import Toast from '../utils/toast.js';
import {validarImagenLocal} from '../utils/images.js';
import { toggleLoading } from '../utils/ui.js';

/* // Vinculamos el input con tu función de utilidad
GeneralEventListener(
    'input-avatar', 
    'change', 
    subirFoto('api/usuarios.php?tipo=subir_temporal', 'avatar', 'avatars', 'perfil')
);
 */

// --- Escuchar el cambio en el input de la foto ---
document.getElementById('input-avatar').addEventListener('change', function(e) {
  // 1. Primero validamos (Usa la función importada)
  const esValido = validarImagenLocal(e);
  
  // 2. Si es válido, ejecutamos TU código de previsualización
  if (esValido && this.files[0]) {
      const reader = new FileReader();
      const preview = document.getElementById('avatar-preview');
      
      reader.onload = function(e) {
          preview.src = e.target.result;
      }
      reader.readAsDataURL(this.files[0]);
  } else {
      // Si no es válido, reseteamos la previa a la default
      document.getElementById('avatar-preview').src = STATIC_URL +'static/img/avatars/default.jpg';
  }
});


// --- Envío del Formulario ---
document.getElementById('form-registro-usuario').addEventListener('submit', function(e) {
  e.preventDefault();

  const form = e.target;
  const btnId = 'btn-registrar-usuario';
  
  // FormData automáticamente incluirá el archivo del input 'avatar' 
  // solo si pasó la validación de arriba (porque si falló, limpiamos el input)
  const formData = new FormData(form); 

  toggleLoading(btnId, true, "Registrando...");

  fetch(BASE_URL + 'api/usuarios.php?tipo=registrar', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      if (data.estatus === true) {
          Toast.fire({ icon: "success", title: data.mensaje });
          form.reset(); 
          document.getElementById('avatar-preview').src = 'static/img/avatars/default.jpg'; 
      } else {
          Toast.fire({ icon: "error", title: data.mensaje });
      }
  })
  .catch(error => {
      console.error('Error:', error);
      Toast.fire({ icon: "error", title: "Error en la conexión" });
  })
  .finally(() => {
      toggleLoading(btnId, false);
  });
});

  