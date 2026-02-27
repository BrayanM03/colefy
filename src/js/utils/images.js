
import { toggleLoading } from '../utils/ui.js';
import  Toast  from '../utils/toast.js';

export function subirFoto(endPoint, fieldName, pathImg, tipo, id_reg = null) {
    return function(event){
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
        const originalSrc = avatarPreview.src;
        const avatarLoader = document.getElementById('avatar-loader'); // El nuevo div
        let fotoNavbar;
        if(tipo == 'perfil'){
            const fotoNavbar = document.getElementById('foto_usuario_navbar');
            if (fotoNavbar) fotoNavbar.style.opacity = '0.3'; // El navbar también "carga"
        }else{
            fotoNavbar = false;
        }
    
        // 1. Activar loaders y opacidad en ambas fotos
        toggleLoading(idBtn, true, "Subiendo...");
        avatarPreview.style.opacity = '0.5';
        avatarLoader.classList.remove('d-none');
    
        const formData = new FormData();
        formData.append(fieldName, file);
        formData.append('id_reg', id_reg);
    
        fetch(BASE_URL + endPoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.estatus === true) {
                const timestamp = new Date().getTime();
                const nuevaRuta = STATIC_URL + `${pathImg}/${data.filename}?t=${timestamp}`;
                
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
    }
   
    // Quitamos el .finally() para controlar el tiempo manualmente en el éxito
}

export function validarImagenLocal(event) {
    const file = event.target.files[0];
    if (!file) return true; // No hay archivo, no hay nada que validar

    const maxMB = 2;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    // 1. Validar Tipo
    if (!allowedTypes.includes(file.type)) {
        Toast.fire({ icon: "error", title: "Solo se permiten imágenes (JPG, PNG, WEBP)" });
        event.target.value = ""; // Limpia el input para que FormData no lo mande
        return false;
    }

    // 2. Validar Tamaño (file.size en bytes)
    if (file.size > maxMB * 1024 * 1024) {
        Toast.fire({ icon: "error", title: `La imagen es muy pesada. Máximo ${maxMB}MB` });
        event.target.value = ""; // Limpia el input
        return false;
    }

    return true; 
}