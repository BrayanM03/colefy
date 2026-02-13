
/**
 * Formatea un objeto Date al formato: "11 Nov 2025 - 4:49 pm"
 * * @param {Date} dateObj El objeto Date a formatear.
 * @returns {string} La fecha formateada como cadena de texto.
 */
export function formatearFechaEspanol(dateObj) {
    if (!(dateObj instanceof Date)) {
        return "Error: Se requiere un objeto Date.";
    }

    // --- Opciones para la FECHA (Día, Mes corto, Año) ---
    const opcionesFecha = {
        day: '2-digit',   // Ejemplo: '10'
        month: 'short', // Ejemplo: 'Nov'
        year: 'numeric' // Ejemplo: '2025'
    };

    // --- Opciones para la HORA (Hora, Minuto, AM/PM) ---
    const opcionesHora = {
        hour: 'numeric',   // Ejemplo: '5'
        minute: '2-digit', // Ejemplo: '05'
        hour12: true       // Formato AM/PM
    };

    // 1. Formatear la parte de la fecha (usamos 'en-GB' para mes abreviado y día/mes/año)
    const fechaFormateada = dateObj.toLocaleDateString('en-GB', opcionesFecha)
                                   // Reemplaza el separador de día/mes/año (como '/' o '-') por un espacio.
                                   .replace(/[/-]/g, ' '); 

    // 2. Formatear la parte de la hora (usamos 'en-US' para el formato 12h y AM/PM)
    const horaFormateada = dateObj.toLocaleTimeString('en-US', opcionesHora)
                                 // Normalizar el AM/PM a minúsculas y quitar espacios extra
                                 .replace(' AM', ' am')
                                 .replace(' PM', ' pm')
                                 .replace(/ /g, ''); 


    // 3. Unir las partes
    return `${fechaFormateada} - ${horaFormateada}`;
}