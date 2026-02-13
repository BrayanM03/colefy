export function DataTableListener(tabla, evento, clase_elemento, callback){
    tabla.on(evento, clase_elemento, function (evento) {
        
        // El 'this' es el botón que se hizo clic
        const botonClickeado = $(this);
        let filaTR = botonClickeado.closest('tr'); 
        
        // 💡 VERIFICACIÓN CRÍTICA PARA RESPONSIVE:
        // Si la fila principal tiene la clase 'child',
        // DataTables almacena la referencia a la fila principal en el hermano anterior (un <tr> oculto).
        // Sin embargo, el método más robusto es usar el row() de DataTables.

        let rowData = tabla.row(filaTR).data();
        
        // Si el resultado es undefined, es probable que estemos en una fila "child" (responsive).
        if (!rowData) {
            // DataTables a menudo coloca la información de la fila principal
            // en la fila que tiene la clase "parent". 
            // Buscamos la fila "parent" inmediatamente anterior al "child row".
            
            // Intentamos subir un nivel más, asumiendo que el botón puede estar en un contenedor
            // dentro de la celda de la fila hija.
            let parentTR = filaTR.prev('.parent');
            
            if (parentTR.length) {
                // Si encontramos la fila 'parent' (la fila que se expandió), usamos esa.
                rowData = tabla.row(parentTR).data();
            } else {
                // Si falla la búsqueda, intentamos obtener el índice de la celda padre
                // (Esto es más complejo, lo anterior suele bastar).
                // Por ahora, nos quedamos con undefined.
            }
        }

        if (rowData && rowData.id) {
            // Llamamos a tu función de lógica
            callback(rowData.id);
        } else {
            console.error("No se pudo obtener el ID del concepto. rowData:", rowData);
        }
    });
}

export function GeneralEventListener(elementSelector, eventox, callback) {
   let elemento = document.getElementById(elementSelector)
    if(elemento) elemento.addEventListener(eventox, callback);
}
