function cargarCuadriculaSemanal() {
    const idGrupo = document.getElementById('filtro_grupo').value;
    const fechaSeleccionada = document.getElementById('filtro_fecha').value;
    
    // Validaciones rápidas
    if (!idGrupo) {
        Swal.fire('Atención', 'Por favor, selecciona un grupo.', 'warning');
        return;
    }
    if (!fechaSeleccionada) {
        Swal.fire('Atención', 'Por favor, selecciona una fecha.', 'warning');
        return;
    }

    const contenedor = document.getElementById('contenedor-cuadricula');
    contenedor.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Construyendo cuadrícula...</p></div>';

    // 1. Calcular el Lunes y el Viernes de la semana seleccionada
    // Añadimos 'T12:00:00' para evitar que el cambio de zona horaria nos mueva el día
    const fechaBase = new Date(fechaSeleccionada + 'T12:00:00');
    const diaSemana = fechaBase.getDay(); 
    // getDay() devuelve 0(Dom), 1(Lun)... 6(Sab). Calculamos la distancia al lunes:
    const diffAlLunes = fechaBase.getDate() - diaSemana + (diaSemana === 0 ? -6 : 1); 
    
    const lunes = new Date(fechaBase.setDate(diffAlLunes));
    
    // Generar un arreglo con los 5 días (Lunes a Viernes) en formato YYYY-MM-DD
    const fechasSemana = [];
    const nombresDias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    
    for (let i = 0; i < 5; i++) {
        const diaActual = new Date(lunes);
        diaActual.setDate(lunes.getDate() + i);
        fechasSemana.push(diaActual.toISOString().split('T')[0]);
    }
    
    const fechaInicio = fechasSemana[0];
    const fechaFin = fechasSemana[4];

    // 2. Hacer la petición al Backend
    fetch(`${BASE_URL}api/asistencias.php?accion=obtenerSemanal&id_grupo=${idGrupo}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
        .then(res => res.json())
        .then(res => {
            if (!res.estatus) {
                contenedor.innerHTML = `<div class="alert alert-danger m-3">${res.mensaje}</div>`;
                return;
            }

            const alumnos = res.data_alumnos;
            const asistencias = res.data_asistencias;

            if (alumnos.length === 0) {
                contenedor.innerHTML = `<div class="alert alert-warning m-3">No hay alumnos inscritos en este grupo.</div>`;
                return;
            }

            // 3. Dibujar la tabla
            let tablaHTML = `
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="min-width: 250px;">Nombre del Alumno</th>
            `;

            // Dibujar las cabeceras de los días (Ej: Lunes 13)
            fechasSemana.forEach((fecha, index) => {
                const dateObj = new Date(fecha + 'T12:00:00');
                const numDia = dateObj.getDate();
                tablaHTML += `<th scope="col" class="text-center" style="width: 90px;">${nombresDias[index]} <br> <span class="text-muted fs-6">${numDia}</span></th>`;
            });

            tablaHTML += `</tr></thead><tbody>`;

            // Dibujar a los alumnos y sus asistencias
            alumnos.forEach(alumno => {
                tablaHTML += `
                <tr>
                    <td>
                        <div class="fw-bold">${alumno.apellido_paterno} ${alumno.apellido_materno} ${alumno.nombre}</div>
                        <small class="text-muted">Matrícula: ${alumno.matricula || 'N/A'}</small>
                    </td>
                `;

                // Recorrer los 5 días para este alumno
                fechasSemana.forEach(fecha => {
                    let contenidoCelda = '<span class="text-muted">-</span>'; // Guión si no hay registro
                    
                    // Comprobar si el alumno tiene asistencia ese día exacto (¡AQUÍ USAMOS EL TRUCO DE PHP!)
                    if (asistencias[alumno.id] && asistencias[alumno.id][fecha] !== undefined) {
                        const estatus = asistencias[alumno.id][fecha];
                        
                        if (estatus == 1) {
                            contenidoCelda = '<span class="badge bg-success w-100 py-2 fs-6">A</span>';
                        } else if (estatus == 2) {
                            contenidoCelda = '<span class="badge bg-warning text-dark w-100 py-2 fs-6">R</span>';
                        } else if (estatus == 0) {
                            contenidoCelda = '<span class="badge bg-danger w-100 py-2 fs-6">F</span>';
                        }
                    }

                    tablaHTML += `<td class="text-center">${contenidoCelda}</td>`;
                });

                tablaHTML += `</tr>`;
            });

            tablaHTML += `</tbody></table>`;
            contenedor.innerHTML = tablaHTML;
        })
        .catch(err => {
            console.error("Error cargando cuadrícula:", err);
            contenedor.innerHTML = `<div class="alert alert-danger m-3">Error de conexión al cargar las asistencias.</div>`;
        });
}