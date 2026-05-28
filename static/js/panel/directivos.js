// static/js/panel/directivos.js

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

// ── Skeleton loader: muestra placeholders mientras carga ──────────────
function mostrarSkeletons() {
    // Stats cards
    document.querySelectorAll('.stat-card h1').forEach(el => {
        el.innerHTML = '<span class="skeleton skeleton-text"></span>';
    });
    document.querySelectorAll('.stat-card .mb-0').forEach(el => {
        el.innerHTML = '<span class="skeleton skeleton-text-sm"></span>';
    });

    // Alertas
    document.getElementById('lista-alertas').innerHTML = `
        <li class="list-group-item border-0 px-0">
            <span class="skeleton skeleton-line"></span>
        </li>
        <li class="list-group-item border-0 px-0 mt-2">
            <span class="skeleton skeleton-line"></span>
        </li>
        <li class="list-group-item border-0 px-0 mt-2">
            <span class="skeleton skeleton-line"></span>
        </li>`;
}

// ── Llenar stats cards ────────────────────────────────────────────────
function llenarStats(data) {
    // Estudiantes
    document.getElementById('stat-estudiantes').textContent    = Number(data.total_alumnos).toLocaleString('es-MX');
    document.getElementById('stat-estudiantes-sub').innerHTML  = `
        <span class="${data.alumnos_nuevos >= 0 ? 'text-success' : 'text-danger'}">
            <i class="mdi mdi-arrow-${data.alumnos_nuevos >= 0 ? 'top' : 'bottom'}-right"></i>
            ${data.alumnos_nuevos >= 0 ? '+' : ''}${data.alumnos_nuevos}%
        </span>
        <span class="text-muted">Desde el mes pasado</span>`;

    // Profesores
    document.getElementById('stat-profesores').textContent    = data.total_profesores;
    document.getElementById('stat-profesores-sub').innerHTML  = `
        <span class="text-muted">${data.total_departamentos} Departamentos</span>`;

    // Grupos
    document.getElementById('stat-grupos').textContent    = data.total_grupos;
    document.getElementById('stat-grupos-sub').innerHTML  = `
        <span class="${data.grupos_diff >= 0 ? 'text-success' : 'text-danger'}">
            ${data.grupos_diff >= 0 ? '+' : ''}${data.grupos_diff}
        </span>
        <span class="text-muted">Ciclo actual</span>`;

    // Ingresos
    document.getElementById('stat-ingresos').textContent    = '$' + Number(data.ingresos_mes).toLocaleString('es-MX', { minimumFractionDigits: 2 });
    document.getElementById('stat-ingresos-sub').innerHTML  = `
        <span class="${data.balance >= 0 ? 'text-success' : 'text-danger'}">
            ${data.balance >= 0 ? 'Al corriente' : 'Con deuda'}
        </span>`;
}

// ── Llenar alertas ────────────────────────────────────────────────────
function llenarAlertas(alertas) {
    const lista = document.getElementById('lista-alertas');

    if (!alertas || alertas.length === 0) {
        lista.innerHTML = `
            <li class="list-group-item border-0 px-0 text-muted">
                <i data-feather="check-circle" class="text-success me-2"></i>
                Sin alertas por el momento
            </li>`;
        feather.replace();
        return;
    }

    const iconos = {
        'asistencia' : { icon: 'alert-circle',  color: 'text-danger'  },
        'pagos'      : { icon: 'dollar-sign',    color: 'text-warning' },
        'inscripcion': { icon: 'user-plus',      color: 'text-info'    },
        'gasto'      : { icon: 'trending-down',  color: 'text-danger'  },
        'default'    : { icon: 'bell',           color: 'text-secondary'}
    };

    lista.innerHTML = alertas.map((alerta, i) => {
        const cfg = iconos[alerta.tipo] || iconos['default'];
        return `
            ${i > 0 ? '<hr>' : ''}
            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                <div class="d-flex align-items-center">
                    <div class="stat ${cfg.color} me-3" style="width:35px; height:35px;">
                        <i data-feather="${cfg.icon}"></i>
                    </div>
                    <div>
                        <strong>${alerta.titulo}</strong>
                        <div class="text-muted small">${alerta.descripcion}</div>
                    </div>
                </div>
                ${alerta.btn_label
                    ? `<button class="btn btn-sm btn-outline-primary" onclick="${alerta.btn_accion}">${alerta.btn_label}</button>`
                    : ''}
            </li>`;
    }).join('');

    feather.replace();
}

// ── Llenar gráfica ────────────────────────────────────────────────────
let chartInstancia = null;

function llenarGrafica(niveles) {
    const ctx = document.getElementById('chartjs-dashboard-pie');
    if (!ctx) return;

    if (chartInstancia) chartInstancia.destroy();

    chartInstancia = new Chart(ctx, {
        type: 'pie',
        data: {
            labels : niveles.map(n => n.nombre),
            datasets: [{
                data           : niveles.map(n => n.total),
                backgroundColor: ['#4e73df', '#f6c23e', '#e74a3b', '#1cc88a', '#36b9cc']
            }]
        },
        options: {
            responsive         : true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    const nivel_classes ={'Preescolar' : 'success', 'Primaria' : 'primary', 'Secundaria' : 'warning', 'Bachillerato': 'danger', 
    'Universidad (Semestral)': 'info',  'Universidad (Cuatrimestral)': 'info'}
    $("#distribucion-nivel").empty()
    niveles.forEach(element => {
        let nivel_class = nivel_classes[element.nombre]
        $("#distribucion-nivel").append(`
            <tr>
                <td><i class="fas fa-circle text-${nivel_class} fa-fw"></i>${element.nombre}</td>
                <td class="text-end">${element.total}</td>
            </tr>
        `)
    });
}

// ── Fetch principal ───────────────────────────────────────────────────
async function cargarPanel() {
    mostrarSkeletons();

    try {
        const response = await fetch(BASE_URL + 'api/panel.php?tipo=dashboard');
        const data     = await response.json();

        if (!data.estatus) {
            Toast.fire({ icon: 'error', title: data.mensaje || 'Error al cargar el panel' });
            return;
        }

        llenarStats(data.stats);
        llenarAlertas(data.alertas);
        llenarGrafica(data.niveles);

    } catch (error) {
        console.error('Error panel:', error);
        Toast.fire({ icon: 'error', title: 'No se pudo conectar con el servidor' });
    }
}

// ── Init ──────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarPanel();

    // Refrescar cada 5 minutos automáticamente
    setInterval(cargarPanel, 5 * 60 * 1000);
});