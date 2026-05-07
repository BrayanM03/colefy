
import { formatearFechaEspanol } from '../utils/dates.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';

GeneralEventListener('btn-ir-paso-dos', 'click', irAlPasoDos)
GeneralEventListener('card-seleccionable', 'click', selectItem)
GeneralEventListener('btn-regresar', 'click', regresarAtras)

const stepDataCache = {
    step2: null,
    step3: null
};

const TURNOS = {
    manana: ["07:00","08:00","09:00","10:00","10:30","11:00","12:00","13:00"],
    tarde:  ["13:00","14:00","15:00","16:00","17:00","18:00","19:00"],
    ambos:  ["07:00","08:00","09:00","10:00","10:30","11:00","12:00",
             "13:00","14:00","15:00","16:00","17:00","18:00"]
};

// Este objeto centraliza todo lo que el usuario elige en cada paso
const horarioData = {
    ciclo: null,
    nivel: null,
    turno: 'manana', //Por Defecto
    docente: null,
    grupo: null,
    materia: null,
    bloques: [] // Aquí guardaremos las horas del Paso 4
};

const asignacionesGuardadas = [];


// Función centralizadora de animación (evita duplicar código en cada paso)
function animarHaciaStep(renderFn, pasoDestino, direccion = 'forward') {
    const card = document.querySelector('.wizard-card');

    const clasesSalida  = direccion === 'back' ? 'animate__fadeOutRight' : 'animate__fadeOutLeft';
    const clasesEntrada = direccion === 'back' ? 'animate__fadeInLeft'   : 'animate__fadeInRight';

    card.classList.add('animate__animated', clasesSalida, 'animate__faster');

    card.addEventListener('animationend', () => {
        card.classList.remove(clasesSalida);

        // Actualizar barra de progreso
        actualizarProgreso(pasoDestino, direccion);

        // Renderizar el paso solicitado
        renderFn();

        card.classList.add(clasesEntrada, 'animate__faster');
        setTimeout(() => card.classList.remove(clasesEntrada), 600);

    }, { once: true });
}

function actualizarProgreso(pasoDestino, direccion) {
    if (direccion === 'back') {
        // Quitar active del paso en el que estábamos
        document.querySelectorAll('.progress-step').forEach(el => {
            const stepNum = parseInt(el.getAttribute('data-step'));
            if (stepNum > pasoDestino) {
                el.classList.remove('active');
            }
        });
    } else {
        document.querySelector(`[data-step="${pasoDestino}"]`)?.classList.add('active');
    }
}

function regresarAtras(paso) {
    switch (paso) {
        case 1:
            animarHaciaStep(renderStep1, 1, 'back');
            break;

        case 2:
            // Reusar datos cacheados, sin volver a hacer fetch
            animarHaciaStep(() => renderStep2(stepDataCache.step2), 2, 'back');

            break;

        case 3:
            animarHaciaStep(() => renderStep3(stepDataCache.step3), 3, 'back');
            break;
    }
}

function renderStep1() {
   

    const cardBody = document.querySelector('.wizard-card .card-body');

    // Reconstruimos el HTML del paso 1
    // Los valores de ciclo/nivel se mantienen en horarioData para pre-seleccionarlos
    cardBody.innerHTML = `
        <span class="badge-step">Paso 1 de 4</span>
        <h2 class="wizard-title mt-3">Configuración inicial del horario</h2>
        <p class="wizard-subtitle">Selecciona el periodo académico, el nivel educativo y el turno para comenzar a armar el horario.</p>

        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <label class="form-label-custom">Ciclo Escolar</label>
                <select class="form-select-airbnb" id="id_ciclo">
                    <!-- Opciones se recargan desde el servidor -->
                </select>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label-custom">Nivel Educativo</label>
                <select class="form-select-airbnb" id="nivel_educativo">
                </select>
            </div>
            <div class="col-md-4 mb-4">
                <label class="form-label-custom">Nivel Educativo</label>
                <select class="form-select-airbnb" id="turno_escolar">
                    <option>Selecciona uno</option>
                    <option value="manana" selected>Matutino</option>
                    <option value="tarde">Vespertino</option>
                    <option value="ambos">Ambos</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button class="btn-airbnb-primary" id="btn-ir-paso-dos">
                Siguiente <i class="fas fa-chevron-right ms-2"></i>
            </button>
        </div>
    `;

    // Recargar los combos y pre-seleccionar lo que el usuario ya había elegido
    fetch('api/catalogos.php?tipo=combos_iniciales')
        .then(res => res.json())
        .then(data => {
            const selectCiclo  = document.getElementById('id_ciclo');
            const selectNivel  = document.getElementById('nivel_educativo');

            data.data.ciclos.forEach(c => {
                const opt = new Option(c.nombre, c.id, false, c.id == horarioData.ciclo);
                selectCiclo.add(opt);
            });

            data.data.niveles.forEach(n => {
                const opt = new Option(n.nombre, n.id, false, n.id == horarioData.nivel);
                selectNivel.add(opt);
            });
        });

    // Re-registrar el listener del botón siguiente
    GeneralEventListener('btn-ir-paso-dos', 'click', irAlPasoDos);
}

function irAlPasoDos() { 
    const ciclo = document.getElementById('id_ciclo').value;
    const nivel = document.getElementById('nivel_educativo').value;
    horarioData.turno = document.getElementById('turno_escolar').value || 'manana';

    if(!ciclo || !nivel) {
        Swal.fire('Atención', 'Por favor selecciona ciclo y nivel', 'warning');
        return;
    }

    horarioData.ciclo = ciclo
    horarioData.nivel = nivel

    // Iniciamos la carga asíncrona
    fetch('api/catalogos.php?tipo=iniciar_flujo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ciclo: ciclo, nivel: nivel })
    })
    .then(res => res.json())
    .then(data => {
        stepDataCache.step2 = data; 
        const card = document.querySelector('.wizard-card');
        
        // 1. Animación de salida (Airbnb style)
        card.classList.add('animate__animated', 'animate__fadeOutLeft' ,'animate__faster');

        card.addEventListener('animationend', () => {
            // 2. Quitamos la animación de salida
            card.classList.remove('animate__fadeOutLeft');

            // 3. Ejecutamos el render con los datos recibidos
            renderStep2(data);

            // 4. Animación de entrada
            card.classList.add('animate__fadeInRight' ,'animate__faster');
            
            // 5. Actualizar visualmente la barra de progreso
            document.querySelector('[data-step="2"]').classList.add('active');

            // Limpiamos la clase de entrada después de que termine para no chocar con futuros pasos
            setTimeout(() => {
                card.classList.remove('animate__fadeInRight');
            }, 600);

        }, { once: true });
    });
}

function renderStep2(data) {
    const cardBody = document.querySelector('.wizard-card .card-body');
    
    // Normalizar datos
    const docentes = data.docentes || [];
    const grupos = data.grupos?.data || [];
    const materias_no = parseInt(data.materias) || 0;
    const alumnos_no = parseInt(data.alumnos_no) || 0;

    // Crear objeto de disponibilidad (true si existe, false si falta)
    const status = {
        docentes: docentes.length > 0,
        grupos: grupos.length > 0,
        materias: materias_no > 0,
        alumnos: alumnos_no > 0
    };

    // Si falta CUALQUIER catálogo crítico, mandamos al Empty State
    if (!status.docentes || !status.grupos || !status.materias || !status.alumnos) {
        
        // Construimos un mensaje dinámico según lo que falte
        let faltantes = [];
        if (!status.docentes) faltantes.push("maestros");
        if (!status.grupos)   faltantes.push("grupos");
        if (!status.materias) faltantes.push("materias");
        if (!status.alumnos)  faltantes.push("alumnos");

        const mensaje = `Para continuar, necesitas registrar: ${faltantes.join(', ')}.`;
        
        return renderEmptyState(cardBody, mensaje, status);
    }

    // Función para generar los items (la usaremos para renderizar y filtrar)
    const generarDocentes = (lista) => lista.map(d => `
        <div class="col-md-4 mb-3 docente-item" data-nombre="${d.nombre.toLowerCase()}">
            <div class="selectable-card text-center" data-type="docente" data-id="${d.id}">
                <div class="avatar-circle mb-2">${d.nombre.charAt(0)}</div>
                <div class="card-label">${d.nombre} ${d.apellido}</div>
            </div>
        </div>
    `).join('');

    const generarGrupos = (lista) => lista.map(g => {
        // Si ya hay un grupo seleccionado (vuelta de "continuar"), bloquearlo
        const esFijo = horarioData.grupo && horarioData.grupo == g.id;
    
        return `
            <div class="col-md-4 mb-3 grupo-item" data-nombre="${g.nombre.toLowerCase()}">
                <div class="selectable-card text-center ${esFijo ? 'selected locked' : ''}" 
                     data-type="grupo" data-id="${g.id}">
                    <div class="icon-box mb-2">
                        <i class="fas ${esFijo ? 'fa-lock' : 'fa-users'}"></i>
                    </div>
                    <div class="card-label">${g.nombre}</div>
                    ${esFijo ? '<small class="text-muted">Grupo fijo</small>' : ''}
                </div>
            </div>
        `;
    }).join('');

    /* const generarGrupos = (lista) => lista.map(g => `
        <div class="col-md-4 mb-3 grupo-item" data-nombre="${g.nombre.toLowerCase()}">
            <div class="selectable-card text-center" data-type="grupo" data-id="${g.id}">
                <div class="icon-box mb-2"><i class="fas fa-users"></i></div>
                <div class="card-label">${g.nombre  }</div>
            </div>
        </div>
    `).join(''); */

    cardBody.innerHTML = `
        <span class="badge-step">Paso 2 de 4</span>
        <h2 class="wizard-title mt-3">Asignación</h2>
        
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0">Selecciona al Docente</h5>
            <input type="text" class="search-input" placeholder="🔍 Buscar maestro..." id="input-buscar-docente">
        </div>
        <div class="row mt-3 custom-scrollbar" style="max-height: 200px; overflow-y: auto;" id="container-docentes">
            ${generarDocentes(data.docentes)}
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0">Selecciona el Grupo</h5>
            <input type="text" class="search-input" placeholder="🔍 Buscar grupo..." id="input-buscar-grupo">
        </div>
        <div class="row mt-3 custom-scrollbar" style="max-height: 200px; overflow-y: auto;" id="container-grupos">
            ${generarGrupos(data.grupos.data)}
        </div>

        <div class="d-flex justify-content-between mt-5">
            <button class="btn-airbnb-secondary btn-regresar" onclick="regresarAtras(1)">Atrás</button>
            <button class="btn-airbnb-primary" id="btn-ir-paso-tres">Siguiente</button>
        </div>
    `;

    GeneralEventListener('btn-ir-paso-tres', 'click', irAlPasoTres)

   // Re-aplicar selecciones previas sobre los elementos recién renderizados
    if (horarioData.docente) {
        const cardDocente = document.querySelector(`.selectable-card[data-type="docente"][data-id="${horarioData.docente}"]`);
        if (cardDocente) selectItem(cardDocente, 'docente', horarioData.docente);
    }

    if (horarioData.grupo) {
        const cardGrupo = document.querySelector(`.selectable-card[data-type="grupo"][data-id="${horarioData.grupo}"]`);
        if (cardGrupo) selectItem(cardGrupo, 'grupo', horarioData.grupo);
    }

}

// Función para seleccionar la tarjeta
function selectItem(element, type, id) {
  
    // Identificar el contenedor padre (docentes o grupos)
    const container = element.closest('.row');
    // Quitar selección previa en ese grupo
    container.querySelectorAll('.selectable-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Marcar el actual
    element.classList.add('selected');
   
    if (type == 'docente') {
        horarioData.docente = id;
    } else if (type == 'grupo') {
        horarioData.grupo = id;
    } else if (type == 'materia') {
        horarioData.materia = id;
    }

}

// Función del Buscador
function filtrarItems(input, className) {
    const filter = input.value.toLowerCase();
    const items = document.getElementsByClassName(className);

    for (let i = 0; i < items.length; i++) {
        const nombre = items[i].getAttribute('data-nombre');
        if (nombre.includes(filter)) {
            items[i].style.display = "";
        } else {
            items[i].style.display = "none";
        }
    }
}

function irAlPasoTres(){

    const docente = horarioData.docente
    const grupo = horarioData.grupo
    const nivel = horarioData.nivel
    if(!docente){
        Swal.fire({icon: 'warning', title:'Selecciona un maestro'})
        return false
    }else if(!grupo){
        Swal.fire({icon: 'warning', title:'Selecciona un grupo'})
        return false
    }



     // Iniciamos la carga asíncrona
     fetch('api/catalogos.php?tipo=segundo_paso', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nivel: nivel })
    })
    .then(res => res.json())
    .then(data => {
        stepDataCache.step3 = data;
        const card = document.querySelector('.wizard-card');
        
        // 1. Animación de salida (Airbnb style)
        card.classList.add('animate__animated', 'animate__fadeOutLeft' ,'animate__faster');

        card.addEventListener('animationend', () => {
            // 2. Quitamos la animación de salida
            card.classList.remove('animate__fadeOutLeft');

            // 3. Ejecutamos el render con los datos recibidos
            renderStep3(data);

            // 4. Animación de entrada
            card.classList.add('animate__fadeInRight' ,'animate__faster');
            
            // 5. Actualizar visualmente la barra de progreso
            document.querySelector('[data-step="3"]').classList.add('active');

            // Limpiamos la clase de entrada después de que termine para no chocar con futuros pasos
            setTimeout(() => {
                card.classList.remove('animate__fadeInRight');
            }, 600);

        }, { once: true });
    });

}

function renderStep3(data) {
    const cardBody = document.querySelector('.wizard-card .card-body');
    
    let materiasHTML = data.materias.map(m => `
        <div class="col-md-4 mb-3 materia-item" data-nombre="${m.nombre.toLowerCase()}">
            <div class="selectable-card text-center" data-id="${m.id}" data-type="materia">
                <div class="icon-box mb-2"><i class="fas fa-book-open"></i></div>
                <div class="card-label">${m.nombre}</div>
                <small class="text-muted d-block">${m.codigo}</small>
            </div>
        </div>
    `).join('');

    cardBody.innerHTML = `
        <span class="badge-step">Paso 3 de 4</span>
        <h2 class="wizard-title mt-3">¿Qué materia impartirá?</h2>
        <p class="wizard-subtitle">Listado de materias disponibles para el nivel seleccionado.</p>
        
        <div class="mt-4 d-flex justify-content-between align-items-center" id="contenedor-materias">
            <h5 class="fw-bold m-0">Selecciona la Materia</h5>
            <input type="text" class="search-input" id="input-buscar-materia" placeholder="🔍 Buscar materia...">
        </div>

        <div class="row mt-4 custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
            ${materiasHTML}
        </div>

        <div class="d-flex justify-content-between mt-5">
        <button class="btn-airbnb-secondary" onclick="regresarAtras(2)">Atrás</button>
        <button class="btn-airbnb-primary" id="btn-ir-paso-cuatro">Configurar Horario <i class="fas fa-clock ms-2"></i></button>
        </div>
    `;

    GeneralEventListener('btn-atras-paso-dos', 'click', irAlPasoDos)
    GeneralEventListener('btn-ir-paso-cuatro', 'click', renderStep4)

    if (horarioData.materia) {
        const cardMateria = document.querySelector(`.selectable-card[data-type="materia"][data-id="${horarioData.materia}"]`);
        if (cardMateria) selectItem(cardMateria, 'materia', horarioData.materia);
    }
    
}

function renderStep4() {
    // Si ya tenemos asignaciones en memoria, renderizamos directo
    if (asignacionesGuardadas.length > 0) {
        _dibujarGrilla();
        return;
    }

    // Si es la primera vez en esta sesión, verificar si hay datos en BD
    fetch(BASE_URL + 'api/catalogos.php?tipo=cargar_conf_prehorario_flujo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_ciclo: horarioData.ciclo, id_grupo: horarioData.grupo })
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.data.length > 0) {
            // Restaurar al array en memoria
            asignacionesGuardadas.push(...resp.data);
            Swal.fire({
                icon: 'info',
                title: 'Configuración restaurada',
                text: `Se cargaron ${resp.data.length} materia(s) previamente guardadas.`,
                timer: 2500,
                showConfirmButton: false
            });
        }
        _dibujarGrilla();
    });
}

function _dibujarGrilla() {
    const cardBody = document.querySelector('.wizard-card .card-body');
    const horas = TURNOS[horarioData.turno] ?? TURNOS.manana;
    const dias  = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];
    const paleta = getPaletaActual();

    // Map de slot ocupado → asignación
    const ocupados = {};
    asignacionesGuardadas.forEach(a => {
        a.bloques.forEach(b => {
            let hora_ft = (b.hora).substring(0,5)
            ocupados[`${b.dia}-${hora_ft}`] = a;
        });
    });
    console.log(ocupados);
    const materiaActualNombre = stepDataCache.step3?.materias
        .find(m => m.id == horarioData.materia)?.nombre ?? '';

    const gridHTML = `
        <div class="table-responsive">
            <table class="table table-bordered schedule-grid">
                <thead>
                    <tr>
                        <th>Hora</th>
                        ${dias.map(d => `<th>${d}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${horas.map(h => `
                        <tr>
                            <td class="time-column">${h}</td>
                            ${dias.map(d => {
                                const key  = `${d}-${h}`;
                                const asig = ocupados[key];
                                console.log(key);
                                console.log(asig);
                                if (asig) {
                                    const color = getColorMateria(asig.materia_id);
                                    return `
                                        <td class="time-slot ocupado"
                                            data-dia="${d}" data-hora="${h}"
                                            style="background:${color.bg}; color:${color.text};"
                                            title="${asig.materia_nombre}">
                                            <div class="slot-inner">
                                                <small>${asig.materia_nombre}</small>
                                            </div>
                                        </td>`;
                                }
                                return `<td class="time-slot libre" data-dia="${d}" data-hora="${h}">
                                            <div class="slot-inner"></div>
                                        </td>`;
                            }).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>`;

    // Leyenda de colores
    const leyendaHTML = Object.values(paleta).length > 0 ? `
        <div class="d-flex flex-wrap gap-2 mt-3">
            ${Object.values(paleta).map(p => `
                <span class="badge-materia" style="background:${p.bg}; color:${p.text};">
                    ${p.nombre}
                </span>`).join('')}
        </div>` : '';

    cardBody.innerHTML = `
        <span class="badge-step">Paso 4 de 4</span>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h2 class="wizard-title m-0">Define el horario</h2>
            <button class="btn-reset-danger" onclick="resetearPrehorario()">
                <i class="fas fa-trash-alt me-1"></i> Reiniciar
            </button>
        </div>
        <p class="wizard-subtitle">
            Materia actual: <strong>${materiaActualNombre}</strong>
        </p>
        ${leyendaHTML}
        ${gridHTML}
        <div class="row mt-5">
            <div class="col-md-3">
                <button class="btn-airbnb-secondary" onclick="regresarAtras(3)">Atrás</button>
            </div>
            <div class="col-md-9 text-end d-flex gap-2 justify-content-end">
                <button class="btn-airbnb-primary" onclick="continuarConfiguracion()">
                    <i class="fas fa-plus-circle me-2"></i> Continuar configurando
                </button>
                <button class="btn-colefy-secondary" onclick="guardarHorarioFinal()">
                    <i class="fas fa-check-circle me-2"></i> Finalizar y Guardar
                </button>
            </div>
        </div>
    `;
}

function renderEmptyState(container, mensaje, catalogos_faltantes) {
    var layout_docentes = ''
    var layout_grupos = ''
    var layout_materias = ''
    var layout_alumnos = ''
    if(!catalogos_faltantes.docentes){
        layout_docentes = `
                <div class="col-md-5">
                    <div class="card h-100 border-dashed p-3">
                        <i class="fas fa-user-tie mb-2 text-primary"></i>
                        <h6>Profesores</h6>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='${BASE_URL}profesores/registrar'">
                            Crear Profesor
                        </button>
                    </div>
                </div>`}

    if(!catalogos_faltantes.grupos){
        layout_grupos = `
                <div class="col-md-5">
                    <div class="card h-100 border-dashed p-3">
                        <i class="fas fa-users mb-2 text-success"></i>
                        <h6>Grupos</h6>
                        <button class="btn btn-sm btn-outline-success" onclick="window.location.href='${BASE_URL}grupos/crear'">
                            Crear Grupo
                        </button>
                    </div>
                </div>`}

    if(!catalogos_faltantes.materias){
        layout_materias = `
                    <div class="col-md-5">
                        <div class="card h-100 border-dashed p-3">
                            <i class="fas fa-book mb-2 text-warning"></i>
                            <h6>Materias</h6>
                            <button class="btn btn-sm btn-outline-warning" onclick="window.location.href='${BASE_URL}materias/registrar'">
                                Crear Materias
                            </button>
                        </div>
                    </div>`}

    if(!catalogos_faltantes.alumnos){
        layout_alumnos = `
                    <div class="col-md-5">
                        <div class="card h-100 border-dashed p-3">
                            <i class="fas fa-book mb-2 text-info"></i>
                            <h6>Alumnos</h6>
                            <button class="btn btn-sm btn-outline-info" onclick="window.location.href='${BASE_URL}alumnos/registrar'">
                                Crear alumnos
                            </button>
                        </div>
                    </div>`}

    container.innerHTML = `
        <div class="text-center py-5 animate__animated animate__fadeIn">
            <div class="mb-4">
                <i class="fas fa-folder-open text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h3 class="fw-bold">¡Empecemos a configurar!</h3>
            <p class="text-muted mb-4">${mensaje}</p>
            
            <div class="row justify-content-center g-3 mb-3">
                ${layout_grupos}
                ${layout_docentes}

            </div>

            <div class="row justify-content-center g-3">
            ${layout_materias}
            ${layout_alumnos} 
            </div>


            <div class="mt-5 pt-3">
                <button class="btn-airbnb-secondary" onclick="regresarAtras(1)">Atrás</button>
                <button class="btn-airbnb-secondary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-2"></i> Ya los creé, reintentar
                </button>
            </div>
        </div>
    `;
}

function continuarConfiguracion() {
    if (horarioData.bloques.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Selecciona al menos un horario' });
        return;
    }

    // Guardar asignación actual
    const asignacion = {
        materia_id:     horarioData.materia,
        materia_nombre: stepDataCache.step3.materias.find(m => m.id == horarioData.materia)?.nombre,
        docente_id:     horarioData.docente,
        grupo_id:       horarioData.grupo,
        bloques:        [...horarioData.bloques]
    };
      // Guardar en BD
      fetch(BASE_URL + 'api/catalogos.php?tipo=guardar_bloques', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_materia:  asignacion.materia_id,
            id_profesor: asignacion.docente_id,
            id_grupo:    asignacion.grupo_id,
            id_ciclo:    horarioData.ciclo,
            bloques:     asignacion.bloques
        })
    }); // fire and forget — no bloqueamos la UI

    asignacionesGuardadas.push(asignacion);

    // Limpiar solo materia, docente y bloques — el GRUPO se conserva
    horarioData.materia = null;
    horarioData.docente = null;  // ← permite elegir otro maestro
    horarioData.bloques = [];

    // Regresar al paso 2
    animarHaciaStep(() => renderStep2(stepDataCache.step2), 2, 'back');
    document.querySelector('[data-step="3"]').classList.remove('active');
    document.querySelector('[data-step="4"]').classList.remove('active');
}

function resetearPrehorario() {
    Swal.fire({
        icon: 'warning',
        title: '¿Reiniciar configuración?',
        text: 'Se eliminará todo el horario que llevas configurado. Esta acción no se puede deshacer.',
        showCancelButton: true,
        confirmButtonText: 'Sí, reiniciar',
        cancelButtonText:  'Cancelar',
        confirmButtonColor: '#e53e3e'
    }).then(result => {
        if (!result.isConfirmed) return;

        fetch(BASE_URL + 'api/catalogos.php?tipo=resetear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_ciclo: horarioData.ciclo, id_grupo: horarioData.grupo })
        })
        .then(() => {
            // Limpiar memoria
            asignacionesGuardadas.length = 0;
            horarioData.materia = null;
            horarioData.docente = null;
            horarioData.bloques = [];

            Swal.fire({ icon: 'success', title: 'Reiniciado', timer: 1500, showConfirmButton: false });
            _dibujarGrilla(); // re-render limpio
        });
    });
}

// Genera un color único por materia_id con buen contraste
function getColorMateria(materiaId) {
    // Ángulo áureo: distribuye los colores uniformemente en el círculo
    const hue = (materiaId * 137.508) % 360;
    const bg  = `hsl(${hue}, 60%, 42%)`;  // saturado y oscuro = texto blanco siempre
    return { bg, text: '#ffffff' };
}

// Construir paleta de todas las materias guardadas (para la leyenda)
function getPaletaActual() {
    const paleta = {};
    asignacionesGuardadas.forEach(a => {
        if (!paleta[a.materia_id]) {
            paleta[a.materia_id] = {
                nombre: a.materia_nombre,
                ...getColorMateria(a.materia_id)
            };
        }
    });
    return paleta;
}

function guardarHorarioFinal() {
    // Si hay bloques sin guardar en el borrador actual, primero los persistimos
    const promesaGuardar = horarioData.bloques.length > 0
        ? fetch(BASE_URL + 'api/catalogos.php?tipo=guardar_bloques', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_materia:  horarioData.materia,
                id_profesor: horarioData.docente,
                id_grupo:    horarioData.grupo,
                id_ciclo:    horarioData.ciclo,
                bloques:     horarioData.bloques
            })
          })
        : Promise.resolve();

    promesaGuardar.then(() => {
        Swal.fire({
            title: '¿Finalizar horario?',
            text:  'El horario quedará guardado y asignado al grupo.',
            icon:  'question',
            showCancelButton:  true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText:  'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(BASE_URL + 'api/catalogos.php?tipo=finalizar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_grupo:       horarioData.grupo,
                    id_ciclo:       horarioData.ciclo,
                    nombre_horario: `Horario ${new Date().toLocaleDateString('es-MX')}`
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.estatus) {
                    // Limpiar estado en memoria
                    asignacionesGuardadas.length = 0;
                    horarioData.bloques  = [];
                    horarioData.materia  = null;
                    horarioData.docente  = null;

                    Swal.fire({
                        icon:  'success',
                        title: '¡Horario guardado!',
                        text:  `Asignado como horario #${data.id_horario}`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error });
                }
            });
        });
    });
}

document.addEventListener('input', function (e) {
    // Si el evento viene del buscador de docentes
    if (e.target && e.target.id === 'input-buscar-docente') {
        filtrarItems(e.target, 'docente-item');
    }
    // Si el evento viene del buscador de grupos
    if (e.target && e.target.id === 'input-buscar-grupo') {
        filtrarItems(e.target, 'grupo-item');
    }
    // Si el evento viene del buscador de materias
    if (e.target && e.target.id === 'input-buscar-materia') {
        filtrarItems(e.target, 'materia-item');
    }    
});

document.addEventListener('click', function (e) {
    // Buscamos si el clic fue en una selectable-card o dentro de una
    const card = e.target.closest('.selectable-card');

    if (card) {
     
        const id = card.getAttribute('data-id');
        const type = card.getAttribute('data-type');
        // Ejecutamos la lógica de selección
        selectItem(card, type, id);
    }
});

document.addEventListener('click', function (e) {
    const slot = e.target.closest('.time-slot');
    if (slot) {
        slot.classList.toggle('selected');
        
        const dia = slot.getAttribute('data-dia');
        const hora = slot.getAttribute('data-hora');
        
        if (slot.classList.contains('selected')) {
            // Agregar al "carrito"
            horarioData.bloques.push({ dia, hora });
        } else {
            // Quitar del "carrito"
            horarioData.bloques = horarioData.bloques.filter(b => !(b.dia === dia && b.hora === hora));
        }
    }
});

// ─── Exponer funciones al scope global (requerido por type="module") ───
window.regresarAtras   = regresarAtras;
window.irAlPasoDos     = irAlPasoDos;
window.irAlPasoTres    = irAlPasoTres;
window.renderStep4     = renderStep4;
window.continuarConfiguracion = continuarConfiguracion;
window.resetearPrehorario = resetearPrehorario;
window.guardarHorarioFinal = guardarHorarioFinal;
//window.guardarHorarioFinal = guardarHorarioFinal;
