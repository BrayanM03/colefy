/**
 * Controla el estado visual de carga de un botón
 * @param {string} id - El ID del botón
 * @param {boolean} loading - true para activar carga, false para restaurar
 * @param {string} textLoading - Texto opcional a mostrar durante la carga
 */
export const toggleLoading = (id, loading, textLoading = "Cargando...") => {
    const btn = document.getElementById(id);
    if (!btn) return;

    // Guardamos el texto original solo la primera vez para poder restaurarlo
    if (loading && !btn.dataset.originalText) {
        btn.dataset.originalText = btn.innerHTML;
    }

    if (loading) {
        btn.disabled = true;
        // Insertamos el spinner de Bootstrap conservando el estilo de AdminKit
        btn.innerHTML = `
            <div class="lds-dual-ring-sm"></div>
            ${textLoading}
        `;
    } else {
        btn.disabled = false;
        // Restauramos el contenido original (iconos feather, texto, etc.)
        btn.innerHTML = btn.dataset.originalText;
        
        // Si usas Feather Icons, es vital refrescar para que vuelvan a aparecer los iconos
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
};