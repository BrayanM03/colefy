
import { formatearFechaEspanol } from '../utils/dates.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';

GeneralEventListener('btn-ir-paso-dos', 'click', irAlPasoDos)
GeneralEventListener('card-seleccionable', 'click', selectItem)

// Este objeto centraliza todo lo que el usuario elige en cada paso
const horarioData = {
    ciclo: null,
    nivel: null,
    docente: null,
    grupo: null,
    materia: null,
    bloques: [] // Aquí guardaremos las horas del Paso 4
};

function irAlPasoDos() {
    const ciclo = document.getElementById('id_ciclo').value;
    const nivel = document.getElementById('nivel_educativo').value;

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
    
    // Función para generar los items (la usaremos para renderizar y filtrar)
    const generarDocentes = (lista) => lista.map(d => `
        <div class="col-md-4 mb-3 docente-item" data-nombre="${d.nombre.toLowerCase()}">
            <div class="selectable-card text-center" data-type="docente" data-id="${d.id}">
                <div class="avatar-circle mb-2">${d.nombre.charAt(0)}</div>
                <div class="card-label">${d.nombre}</div>
            </div>
        </div>
    `).join('');

    const generarGrupos = (lista) => lista.map(g => `
        <div class="col-md-4 mb-3 grupo-item" data-nombre="${g.nombre.toLowerCase()}">
            <div class="selectable-card text-center" data-type="grupo" data-id="${g.id}">
                <div class="icon-box mb-2"><i class="fas fa-users"></i></div>
                <div class="card-label">${g.nombre  }</div>
            </div>
        </div>
    `).join('');

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
            <button class="btn-airbnb-secondary" onclick="location.reload()">Atrás</button>
            <button class="btn-airbnb-primary" id="btn-ir-paso-tres">Siguiente</button>
        </div>
    `;

    GeneralEventListener('btn-ir-paso-tres', 'click', irAlPasoTres)
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
            <button class="btn-airbnb-secondary" id="btn-atras-paso-dos" onclick="irAlPasoDos()">Atrás</button>
            <button class="btn-airbnb-primary" id="btn-ir-paso-cuatro">Configurar Horario <i class="fas fa-clock ms-2"></i></button>
        </div>
    `;

    GeneralEventListener('btn-atras-paso-dos', 'click', irAlPasoDos)
    GeneralEventListener('btn-ir-paso-cuatro', 'click', renderStep4)
    
}

function renderStep4() {
    const cardBody = document.querySelector('.wizard-card .card-body');
    
    // Definimos las horas (puedes ajustarlas según el turno)
    const horas = ["07:00", "08:00", "09:00", "10:00", "10:30", "11:00", "12:00", "13:00"];
    const dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];
    document.querySelector('[data-step="4"]').classList.add('active');

    let gridHTML = `
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
                            ${dias.map(d => `
                                <td class="time-slot" data-dia="${d}" data-hora="${h}">
                                    <div class="slot-inner"></div>
                                </td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    cardBody.innerHTML = `
        <span class="badge-step">Paso 4 de 4</span>
        <h2 class="wizard-title mt-3">Define el horario</h2>
        <p class="wizard-subtitle">Haz clic en los espacios disponibles para asignar la materia.</p>
        
        ${gridHTML}

        <div class="d-flex justify-content-between mt-5">
            <button class="btn-airbnb-secondary" onclick="irAlPasoTres()">Atrás</button>
            <button class="btn-airbnb-primary" onclick="guardarHorarioFinal()">
                <i class="fas fa-check-circle me-2"></i> Finalizar y Guardar
            </button>
        </div>
    `;
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
        console.log("Bloques seleccionados:", horarioData.bloques);
    }
});

