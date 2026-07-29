<?php
$controller_permiso->validarAcceso(1, CPermiso::VER_ASISTENCIA_QR->value);
require_once __DIR__ . '/../controllers/GrupoController.php';
$grupo_control = new GrupoController();

$data_grupo =$grupo_control->combo_grupos_profesor(1);

include "vistas/general/header.php";

?>
<!-- Importar la librería gratuita (puedes descargar el archivo para alojarlo tú mismo) -->
<script src="https://unpkg.com/html5-qrcode"></script>

<div class="card">
    Grupo: 
</div>

<!-- El div donde se renderizará la cámara -->
<div id="lector_qr" style="width: 100%; max-width: 500px; margin: auto;"></div>

<!-- Tarjeta flotante para el feedback visual (oculta por defecto) -->
<div id="alerta_alumno" style="display: none; position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); z-index: 9999; align-items: center; gap: 15px;">
    <img id="alerta_foto" src="" alt="Foto" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
    <div>
        <h4 id="alerta_nombre" style="margin: 0; font-size: 16px;">Nombre del Alumno</h4>
        <small id="alerta_mensaje" style="margin: 0; opacity: 0.9;">Asistencia registrada</small>
    </div>
</div>

<!-- Inputs ocultos para que el JS sepa en qué clase estamos -->
<input type="hidden" id="qr_id_grupo" value="<?= $_GET['id_grupo']?>">
<input type="hidden" id="qr_id_dh" value="<?= $_GET['id_dh']?>">
<input type="hidden" id="qr_tipo" value="x">
<?php
        include "vistas/general/footer.php"
        ?>
         <?php
        include "vistas/general/scripts.php"
    ?>
<script>
    // Variables para evitar múltiples lecturas del mismo alumno en un segundo
let ultimoEscaneado = "";
let tiempoUltimoEscaneo = 0;
let scanner_qr = null;
iniciarLectorQR()
function iniciarLectorQR() {
    // Configuración de la cámara (usar la cámara trasera por defecto)
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    
    scanner_qr = new Html5Qrcode("lector_qr");
    
    scanner_qr.start(
        { facingMode: "environment" }, // Intenta forzar cámara trasera en celulares
        config,
        procesarEscaneo, // Función de éxito
        (errorMessage) => {
            // Ignorar errores de "código no encontrado", son normales mientras busca
        }
    ).catch((err) => {
        console.error("Error al iniciar la cámara:", err);
        alert("Por favor, permite el acceso a la cámara.");
    });
}

// Función principal que se dispara cuando detecta un QR
async function procesarEscaneo(texto_decodificado, resultado) {
    const ahora = Date.now();
    
    // Validar efecto rebote (3000 milisegundos = 3 segundos de gracia por alumno)
    if (texto_decodificado === ultimoEscaneado && (ahora - tiempoUltimoEscaneo) < 3000) {
        return; // Ignoramos la lectura porque es el mismo gafete
    }
    
    ultimoEscaneado = texto_decodificado;
    tiempoUltimoEscaneo = ahora;

    // Opcional: Reproducir un sonido de 'Beep'
    // const audio = new Audio('ruta/a/tu/sonido-beep.mp3');
    // audio.play().catch(e => console.log('Autoplay bloqueado por el navegador'));

    // Armar el payload para el backend
    const payload = {
        qr_data: texto_decodificado, // La matrícula
        id_grupo: document.getElementById('qr_id_grupo').value,
        id_dh: document.getElementById('qr_id_dh').value,
        tipo: document.getElementById('qr_tipo').value
    };
    console.log(payload);
    Swal.fire({
        title: payload.qr_data
    })

    return false;

   /*  try {
        const respuesta = await fetch('api/asistencias.php?tipo=registrar_qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await respuesta.json();

        if (data.estatus) {
            // Mostrar la tarjeta flotante con la foto y el nombre
            mostrarFeedbackVisual(data.alumno.nombre, data.alumno.foto, data.mensaje);
        } else {
            // Mostrar error (ej. Alumno no existe)
            alert(data.mensaje); // O cambiarlo por un toast de error rojo
        }

    } catch (error) {
        console.error("Error de conexión al registrar:", error);
    } */
}

// Función para mostrar la tarjeta flotante por 2 segundos
function mostrarFeedbackVisual(nombre, fotoUrl, mensaje) {
    const alerta = document.getElementById('alerta_alumno');
    document.getElementById('alerta_nombre').innerText = nombre;
    document.getElementById('alerta_mensaje').innerText = mensaje;
    
    // Asegurar que la ruta de la foto sea correcta según tu estructura de carpetas
    document.getElementById('alerta_foto').src = 'public/img/alumnos/' + fotoUrl; 
    
    // Mostrar la alerta
    alerta.style.display = 'flex';
    alerta.style.background = '#4caf50'; // Verde de éxito

    // Ocultarla automáticamente después de 2.5 segundos
    setTimeout(() => {
        alerta.style.display = 'none';
    }, 2500);
}

// Función para apagar la cámara cuando el maestro cierre el modal de pasar lista
function detenerLectorQR() {
    if (scanner_qr) {
        scanner_qr.stop().then(() => {
            console.log("Cámara apagada");
        }).catch((err) => {
            console.error("Error al detener la cámara:", err);
        });
    }
}
</script>