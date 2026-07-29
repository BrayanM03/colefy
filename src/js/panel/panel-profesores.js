 

function setearTablaGrupo(id_grupo){
    let area = $("#area-grupo")
    let id_ciclo = $("#ciclo").attr('id_ciclo');
    document.querySelectorAll('.tarjeta-grupo').forEach(tarjeta => {
        tarjeta.classList.remove('tarjeta_activa');
    });
    const tarjeta = $("#tarjeta-" + id_grupo).addClass('tarjeta_activa')
    area.empty();
    area.append(`
        <div class="row">
            <div class="col-12 text-center">
            <img src="${BASE_URL}/static/img/loading.gif" style="width:30px;"></img>
            </div>
        </div>
    `)

    setTimeout(()=>{
        area.empty();
        area.append(`
        <table class="table table-hover">
            <thead>
                <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>1er periodo</th>
                <th>2do periodo</th>
                <th>3er periodo</th>
                </tr>
            </thead>
            <tbody id="tbody-alumnos"></tbody>
        </table>
    `);

    $.ajax({
        type: "post",
        url: BASE_URL + "api/grupos.php?tipo=grupo_calificaciones",
        data: {id_grupo, id_ciclo},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                response.data.forEach(element => {
                    $("#tbody-alumnos").append(`
                    <tr>
                        <td>${element.id}</td>
                        <td>${element.nombre} ${element.apellido_paterno} ${element.apellido_materno}</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    `)
                    
                });
            }
        }
    });
    }, 700)
    


}

function verHorarioProfesor() {
    // Hacemos la petición a tu API
    fetch(BASE_URL + 'api/horarios.php?tipo=obtener_mi_horario') // Ajusta la URL a tu ruta real
        .then(response => response.json())
        .then(res => {
            if (!res.estatus) {
                Swal.fire('Error', 'No se pudo cargar el horario', 'error');
                return;
            }

            const horario = res.data;
            let htmlContent = '<div class="text-start mt-3">';

            // Si el objeto está vacío, el profe no tiene clases asignadas
            if (Object.keys(horario).length === 0) {
                htmlContent += `
                    <div class="alert alert-info text-center" role="alert">
                        No tienes clases asignadas en este ciclo activo.
                    </div>`;
            } else {
                // Recorremos cada día (Lunes, Martes, etc.)
                for (const dia in horario) {
                    // Título del día con estilo
                    htmlContent += `<h5 class="mb-2 text-primary border-bottom pb-1 fw-bold" style="margin-top: 1.5rem;">${dia}</h5>`;
                    
                    // Iniciamos la tabla para ese día
                    htmlContent += `
                        <table class="table table-sm table-bordered table-hover" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Hora</th>
                                    <th width="45%">Materia</th>
                                    <th width="25%">Grupo</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    // Llenamos las filas con las clases de ese día
                    horario[dia].forEach(clase => {
                        htmlContent += `
                            <tr>
                                <td class="align-middle">
                                    <i data-feather="clock" class="text-muted" style="width: 14px; height: 14px; margin-right: 4px;"></i> 
                                    ${clase.hora} - ${clase.hora_fin}
                                </td>
                                <td class="align-middle fw-semibold text-secondary">${clase.materia}</td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-info text-white w-100">${clase.grupo}</span>
                                </td>
                            </tr>
                        `;
                    });

                    htmlContent += `</tbody></table>`;
                }
            }
            
            htmlContent += '</div>';

            // Disparamos el SweetAlert con el HTML construido
            Swal.fire({
                title: 'Mi Horario de Clases',
                html: htmlContent,
                width: '650px', // Lo hacemos más ancho para que la tabla no se aplaste
                showCloseButton: true,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#3085d6',
                didOpen: () => {
                    // Si usas Feather Icons en tu panel (que creo que sí por el código anterior), los renderizamos
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                }
            });
        })
        .catch(error => {
            console.error("Error obteniendo el horario:", error);
            Swal.fire('Error', 'Hubo un problema de conexión', 'error');
        });
}

function cargarClasesDelDia() {
    // Ajusta esta ruta a tu endpoint real de Colefy
    fetch(BASE_URL + 'api/horarios.php?tipo=obtener_clases_hoy') 
        .then(r => r.json())
        .then(res => {
            const contenedor = document.getElementById('contenedor-clases-hoy');
            
            // Caso: El profesor tiene el día libre
            if(res.data.length >= 5){
                btn_display = '';
            }else{
                btn_display = 'd-none'
            }
            if (!res.estatus || res.data.length === 0) {
                contenedor.innerHTML = `
                    <div class="col-12 mt-3">
                        <div class="alert alert-light border text-center p-4" role="alert">
                            <i data-feather="coffee" class="text-muted mb-2" style="width:32px; height:32px;"></i>
                            <h5 class="text-secondary mt-2">Día libre</h5>
                            <p class="mb-0">No tienes clases programadas para hoy (${res.dia}).</p>
                        </div>
                    </div>`;
                if (typeof feather !== 'undefined') feather.replace();
                return;
            }
            
            let html = `
            <div class="position-relative mt-3 px-4">
            <button id="btn-scroll-izq" class="${btn_display} btn btn-primary rounded-circle position-absolute top-50 translate-middle-y z-3 shadow" style="left: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index:999;">
                <i data-feather="chevron-left"></i>
            </button>
            <div id="carrusel-tarjetas" class="d-flex flex-nowrap overflow-hidden py-2" style="scroll-behavior: smooth; gap: 1rem;">
            `;// <div class="row mt-3">
            
            res.data.forEach(clase => {
                // Variables para diseño dinámico según el estado
                let bordeCard = '';
                let badgeEstado = '';
                let botonLista = '';
                console.log(clase);
                
                let claseTippy = 'titulo-materia-tippy';
                let estilo_tit_materia='style="color:#2AA63E;"'
                let tit_materia= clase.materia+'<i data-feather="check-circle" style="width:12px; height:12px;"></i>'
                if(clase.asistencia_tomada == 0){
                    claseTippy =''
                    estilo_tit_materia=''
                    tit_materia= clase.materia
                }
                if (clase.estado === 'actual') {
                    // Diseño resaltado para la clase que está ocurriendo AHORA
                    bordeCard = 'border-primary border-2 shadow-sm';
                    badgeEstado = '<span class="badge bg-primary mb-2">Clase actual</span>';
                    botonLista = `<button class="btn btn-primary w-100" onclick="pasarLista(${clase.id_grupo}, ${clase.id_materia}, '${clase.materia}', '${clase.grupo}', ${clase.id_dh}, 2)">Pasar lista</button>`;
                    if(clase.tipo_asistencia ==1){
                        botonLista = `<button class="btn btn-outline-info w-100" disabled>Pasar lista</button>`; 
                    }
                } else if (clase.estado === 'pasada') {
                    // Diseño opaco para clases que ya terminaron
                    bordeCard = 'border-light bg-light text-muted opacity-75';
                    badgeEstado = '<span class="badge bg-secondary mb-2">Finalizada</span>';
                    botonLista = `<button class="btn btn-outline-secondary btn-sm w-100" onclick="verLista(${clase.id_grupo})">Ver lista</button>`;
                } else {
                    // Diseño estándar para clases futuras
                    bordeCard = 'border';
                    badgeEstado = '<span class="badge bg-info text-dark mb-2">Próxima</span>';
                    // El botón está deshabilitado para que no pasen lista antes de tiempo
                    botonLista = `<button class="btn btn-outline-info w-100" disabled >Pasar lista</button>`; 
                }
                console.log(botonLista);
                
                html += `
                <div class="col-12 col-md-3 mb-3">
                    <div class="card h-100 ${bordeCard}">
                        <div class="card-body d-flex flex-column">
                            <div>${badgeEstado}</div>
                            <h5 class="card-title mb-1 fw-bold ${claseTippy} w-50" ${estilo_tit_materia}>${tit_materia}</h5>
                            <span class="text-muted small mb-2 d-block">
                                <i data-feather="clock" style="width:12px; height:12px;"></i> ${clase.hora}
                            </span>
                            <span class="d-block mb-3 text-secondary" style="font-size: 0.9rem;">
                                Grupo: <strong>${clase.grupo}</strong>
                            </span>
                            <div class="mt-auto">
                                ${botonLista}
                            </div>
                        </div>
                    </div>
                </div>`;

            });

           

            
            html += `</div><button id="btn-scroll-der" class="${btn_display} btn btn-primary rounded-circle position-absolute top-50 translate-middle-y z-3 shadow" style="right: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i data-feather="chevron-right"></i>
        </button></div>`;

            contenedor.innerHTML = html;
            
            if (typeof feather !== 'undefined') feather.replace();
            //Tooltips
            tippy(`.titulo-materia-tippy`, {
                content: 'Asistencia tomada!',
            });

            // 5. Lógica de los botones de Scroll
            const track = document.getElementById('carrusel-tarjetas');
            const btnIzq = document.getElementById('btn-scroll-izq');
            const btnDer = document.getElementById('btn-scroll-der');

            // Desplaza 280px (ancho de tarjeta + gap)
            btnIzq.addEventListener('click', () => {
                track.scrollBy({ left: -280, behavior: 'smooth' });
            });

            btnDer.addEventListener('click', () => {
                track.scrollBy({ left: 280, behavior: 'smooth' });
            });
        })
        .catch(error => {
            console.error("Error cargando las clases:", error);
            document.getElementById('contenedor-clases-hoy').innerHTML = `
                <div class="alert alert-danger">Hubo un problema al cargar tus clases de hoy.</div>`;
        });
}

// Ejecutar al cargar la vista del panel del maestro
document.addEventListener('DOMContentLoaded', cargarClasesDelDia);
//document.addEventListener('DOMContentLoaded', generarCarruselClasesEstaticas);

function generarCarruselClasesEstaticas() {
    const contenedor = document.getElementById('contenedor-clases-hoy');
    
    // 1. Datos estáticos de prueba (Mock)
    const clasesMock = [
        { estado: 'pasada', materia: 'Historia Universal', hora: '07:00 - 08:00', grupo: '1° A', id_grupo: 1 },
        { estado: 'pasada', materia: 'Matemáticas I', hora: '08:00 - 09:00', grupo: '1° A', id_grupo: 1 },
        { estado: 'actual', materia: 'Física', hora: '09:00 - 10:00', grupo: '3° B', id_grupo: 3 },
        { estado: 'proxima', materia: 'Química', hora: '10:30 - 11:30', grupo: '3° B', id_grupo: 3 },
        { estado: 'proxima', materia: 'Biología', hora: '11:30 - 12:30', grupo: '2° C', id_grupo: 2 },
        { estado: 'proxima', materia: 'Tutoría', hora: '12:30 - 13:30', grupo: '2° C', id_grupo: 2 }
    ];

    // 2. Estructura HTML del Carrusel
    // Usamos position-relative para colocar los botones flotantes
    // y un contenedor con d-flex y flex-nowrap para que las tarjetas se formen en fila
    let html = `
        <div class="position-relative mt-3 px-4">
            <button id="btn-scroll-izq" class="btn btn-primary rounded-circle position-absolute top-50 translate-middle-y z-3 shadow" style="left: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index:999;">
                <i data-feather="chevron-left"></i>
            </button>

            <div id="carrusel-tarjetas" class="d-flex flex-nowrap overflow-hidden py-2" style="scroll-behavior: smooth; gap: 1rem;">
    `;

    // 3. Generar las tarjetas
    clasesMock.forEach(clase => {
        let bordeCard = '';
        let badgeEstado = '';
        let botonLista = '';
        if (clase.estado === 'actual') {
            bordeCard = 'border-primary border-2 shadow-sm'; 
            badgeEstado = '<span class="badge bg-primary mb-2">Clase actual</span>';
            botonLista = `<button class="btn btn-primary w-100" onclick="pasarLista(${clase.id_grupo}, 2, '${clase.materia}', '${clase.grupo}', ${clase.id_dh}, 2)">Pasar lista</button>`;
            if( clase.tipo_asistecia ==2){
                botonLista = `<button class="btn btn-outline-info w-100" disabled>Pasar lista</button>`; 
            }
        } else if (clase.estado === 'pasada') {
            bordeCard = 'border-light bg-light text-muted opacity-75';
            badgeEstado = '<span class="badge bg-secondary mb-2">Finalizada</span>';
            botonLista = `<button class="btn btn-outline-secondary btn-sm w-100" onclick="verLista(${clase.id_grupo})">Ver lista</button>`;
        } else {
            bordeCard = 'border';
            badgeEstado = '<span class="badge bg-info text-dark mb-2">Próxima</span>';
            botonLista = `<button class="btn btn-outline-info w-100" disabled>Pasar lista</button>`; 
        }
        
        // NOTA: Cambiamos las clases de columna (col-12 col-md-3) por un ancho mínimo fijo (min-width)
        html += `
            <div class="card h-100 ${bordeCard} flex-shrink-0" style="min-width: 260px; max-width: 260px;">
                <div class="card-body d-flex flex-column">
                    <div>${badgeEstado}</div>
                    <h5 class="card-title mb-1 fw-bold text-truncate">${clase.materia}</h5>
                    <span class="text-muted small mb-2 d-block">
                        <i data-feather="clock" style="width:12px; height:12px;"></i> ${clase.hora}
                    </span>
                    <span class="d-block mb-3 text-secondary" style="font-size: 0.9rem;">
                        Grupo: <strong>${clase.grupo}</strong>
                    </span>
                    <div class="mt-auto">
                        ${botonLista}
                    </div>
                </div>
            </div>`;
    });

    html += `
            </div> <button id="btn-scroll-der" class="btn btn-primary rounded-circle position-absolute top-50 translate-middle-y z-3 shadow" style="right: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i data-feather="chevron-right"></i>
            </button>
        </div>
    `;

    // 4. Inyectar en el DOM
    contenedor.innerHTML = html;

    // Renderizar iconos
    if (typeof feather !== 'undefined') feather.replace();

    // 5. Lógica de los botones de Scroll
    const track = document.getElementById('carrusel-tarjetas');
    const btnIzq = document.getElementById('btn-scroll-izq');
    const btnDer = document.getElementById('btn-scroll-der');

    // Desplaza 280px (ancho de tarjeta + gap)
    btnIzq.addEventListener('click', () => {
        track.scrollBy({ left: -280, behavior: 'smooth' });
    });

    btnDer.addEventListener('click', () => {
        track.scrollBy({ left: 280, behavior: 'smooth' });
    });
}

// Ajusta tu botón en el carrusel para que envíe grupo y materia:
// onclick="pasarLista(${clase.id_grupo}, ${clase.id_materia}, '${clase.materia}', '${clase.grupo}')"

function pasarLista(id_grupo, id_materia, nombre_materia, nombre_grupo, id_dh, tipo) {
    let id_ciclo = $("#ciclo").attr('id_ciclo');    
    const contenedor_lista = document.getElementById('contenedor-lista-alumnos');

    // 3. Consultar los alumnos de ese grupo al backend
    fetch(`${BASE_URL}api/grupos.php?tipo=obtener_alumnos_grupo&id_grupo=${id_grupo}&id_ciclo=${id_ciclo}&id_dh=${id_dh}&tipo_modalidad=${tipo}`)
        .then(res => res.json())
        .then(data => {
            
            if(!data.estatus || data.data.length === 0) {
               
                html_empty_alumns = '<div class="alert alert-warning">No hay alumnos registrados en este grupo.</div>';
                Swal.fire({
                    icon: 'error',
                    html: html_empty_alumns
                })
                return;
            }

            Swal.fire({
                width: '700px',
                html: `
                    <div class="container-fluid">

                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="fw-bold" id="lista-materia-nombre">Materia</span>
                                <span class="text-muted" id="lista-grupo-nombre">Grupo</span>
                            </div>
                            
                            <form id="formAsistencia">
                                <input type="hidden" id="id_grupo_asistencia" name="id_grupo">
                                <input type="hidden" id="id_materia_asistencia" name="id_materia">
                                
                                <div id="contenedor-lista-alumnos" class="list-group list-group-flush">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando alumnos...</span>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        <div class="row mb-3 mt-3">
                            <div class="col-9">
                                <!---<label>¿Deseas guardar este pase de lista para todo el día?</label>-->
                            </div>
                            <div class="col-3">
                            <div class="row">
                                <!----<div class="col-4">
                                    <label>No</label>
                                </div>
                                <div class="col-4">
                                    <div class="form-check form-switch mb-0" title="Modo oscuro">
                                     <input id="asistencia-grupal" class="form-check-input" type="checkbox" id="darkmode-switch" role="switch">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label>Sí</label>
                                 </div>-->
                            </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <a href="${BASE_URL}asistencia_qr/${id_grupo}/${id_dh}" target="_blank"><button type="button" class="btn btn-primary"><i class="fa-solid fa-qrcode"></i> Asistencia QR</button></a>
                            <button type="button" id="btn-guardar-asistencia" class="btn btn-success" onclick="guardarAsistencias(${id_dh})">Guardar Asistencia</button>
                        </div>
                    </div>
                `,
                didOpen:
                ()=>{

                    const contenedor_lista = document.getElementById('contenedor-lista-alumnos');
                    contenedor_lista.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
                   
                                    // 1. Llenar los datos visuales y ocultos del modal
                    document.getElementById('lista-materia-nombre').innerText = nombre_materia;
                    document.getElementById('lista-grupo-nombre').innerText = 'Grupo: ' + nombre_grupo;
                    document.getElementById('id_grupo_asistencia').value = id_grupo;
                    document.getElementById('id_materia_asistencia').value = id_materia;

                    const asistenciasGuardadas = data.data_asis || [];
                   

                    // 4. Dibujar la lista con un "Switch" por alumno (Encendido por defecto)
                    let htmlLista = '';
                    data.data.forEach((alumno, index) => {

                        // 1. Buscamos si el alumno ya tiene un registro guardado en data_asis
                        const registroPrevio = asistenciasGuardadas.find(a => a.id_alumno === alumno.id_alumno);
                                        
                        // 2. Si existe un registro, tomamos su estatus. Si no, por defecto es 1 (Asistencia)
                        const estatusActual = registroPrevio ? registroPrevio.estatus : 1;

                        const checkA = (estatusActual == 1) ? 'checked' : '';
                        const checkR = (estatusActual == 2) ? 'checked' : '';
                        const checkF = (estatusActual == 0) ? 'checked' : '';

                        //Marcador visual para que el maestro vea que ya se había guardado
                        console.log(alumno.id);
                        const badgeRegistrado = registroPrevio 
                        ? `<span class="badge bg-light text-success border border-success ms-2 py-1" style="font-size: 0.65rem;"><i data-feather="check" style="width:10px; height:10px;"></i> Guardado</span>` 
                        : '';

                        htmlLista += `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle text-primary fw-bold d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                    ${index + 1}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">${alumno.nombre} ${alumno.apellido_paterno} ${alumno.apellido_materno} ${badgeRegistrado}</h6>
                                    <small class="text-muted">Matrícula: ${alumno.id_interno || 'N/A'}</small>
                                </div>
                            </div>
                            
                            <div class="btn-group" role="group" aria-label="Control de asistencia">
                                <input type="radio" class="btn-check input-asistencia" name="asistencia[${alumno.id_alumno}]" id="asist_${alumno.id_alumno}" value="1" ${checkA} autocomplete="off">
                                <label class="btn btn-outline-success btn-sm px-3" for="asist_${alumno.id_alumno}" title="Asistencia">A</label>

                                <input type="radio" class="btn-check input-asistencia" name="asistencia[${alumno.id_alumno}]" id="retardo_${alumno.id_alumno}" value="2" ${checkR} autocomplete="off">
                                <label class="btn btn-outline-warning btn-sm px-3" for="retardo_${alumno.id_alumno}" title="Retardo">R</label>

                                <input type="radio" class="btn-check input-asistencia" name="asistencia[${alumno.id_alumno}]" id="falta_${alumno.id_alumno}" value="0" ${checkF} autocomplete="off">
                                <label class="btn btn-outline-danger btn-sm px-3" for="falta_${alumno.id_alumno}" title="Falta">F</label>
                            </div>
                        </div>`;
                    });

                    contenedor_lista .innerHTML = htmlLista;
                    
                },
                allowOutsideClick: false,
                showCloseButton:true,
                showConfirmButton:false,
                showCancelButton:false
            });
           
        })
        .catch(err => {
            console.error("Error al cargar alumnos:", err);
            contenedor_lista .innerHTML = '<div class="alert alert-danger">Error al cargar la lista.</div>';
        });
}

function guardarAsistencias(id_dh) {
    // 1. Deshabilitar el botón para evitar dobles clics
    const btnGuardar = document.querySelector('#btn-guardar-asistencia');
   /*  btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
    btnGuardar.disabled = true; */

    // 2. Obtener datos generales (los inputs hidden que pusimos en el modal)
    const id_grupo = document.getElementById('id_grupo_asistencia').value;
    const id_materia = document.getElementById('id_materia_asistencia').value;
    
    // Aquí defines si la escuela trabaja por grupo (1) o por materia (2)
    // Para este prototipo lo mandaremos como 2 (Por materia/clase)
    //let asistencia_grupal = document.getElementById('asistencia-grupal').checked;
    
    const tipo =null; /*!asistencia_grupal ? 2 : 1; */

    // 3. Recolectar las asistencias de los alumnos
    const asistencias = [];
    // Seleccionamos solo los radio buttons que están "checked"
    const inputsAsistencia = document.querySelectorAll('.input-asistencia:checked');
    console.log(inputsAsistencia);
    inputsAsistencia.forEach(input => {
        // El name tiene formato "asistencia[ID_ALUMNO]"
        const id_alumno = input.name.match(/\[(.*?)\]/)[1]; 
        const estatus = input.value;
        
        asistencias.push({
            id_alumno: id_alumno,
            estatus: estatus
        });
    });

    console.log(asistencias);
    // 4. Armar el paquete JSON
    const payload = {
        id_grupo: id_grupo,
        id_materia: id_materia,
        tipo: tipo,
        asistencias: asistencias,
        id_dh: id_dh
    };

    // 5. Enviar al servidor
    fetch(BASE_URL + 'api/asistencias.php?tipo=guardar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.estatus) {
            Swal.fire({
                icon: 'success',
                title: '¡Listo!',
                text: 'Asistencia guardada correctamente.',
                timer: 2000,
                showConfirmButton: false
            });
            // Opcional: Recargar las clases para que el botón diga "Ver lista" en vez de "Pasar lista"
            cargarClasesDelDia(); 
        } else {
            Swal.fire('Error', res.mensaje, 'error');
        }
    })
    .catch(err => {
        console.error("Error:", err);
        Swal.fire('Error', 'Falla de conexión al guardar.', 'error');
    })
    .finally(() => {
        // Restaurar el botón
        btnGuardar.innerHTML = 'Guardar Asistencia';
        btnGuardar.disabled = false;
    });
}

