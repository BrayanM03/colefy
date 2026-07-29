import { GeneralEventListener } from "../utils/listeners.js";

$(document).ready(function () {
  //Cargando listeners
  //GeneralEventListener('mostrar-configuraciones', 'click', ventanaConfiguraciones)
  const area_permisos = $("#area-permisos");

  area_permisos.empty().append(`
        <div class="row">
            <div class="col-12 text-center">
                <dotlottie-wc
                    class="m-auto"
                    src="https://lottie.host/16abd1c5-90bb-4e18-b98b-a616ac4b71ff/Y552obLciD.lottie"
                    style="width: 120px;height: 120px"
                    autoplay
                    loop></dotlottie-wc>
            </div>
        </div>
    `);

  setTimeout(() => {
    setearPermiso();
  }, 1400);

  function setearPermiso() {
    $.ajax({
      type: "post",
      url: BASE_URL + "api/permisos.php?tipo=ver_lista_permisos",
      data: "data",
      dataType: "json",
      success: function (response) {
        if (response.estatus) {
          area_permisos.empty();

          const permisosAgrupados = response.data.reduce((acc, permiso) => {

            const idCategoria = permiso.id_categoria || 0;
        
            if (!acc[idCategoria]) {
                acc[idCategoria] = {
                    nombre: permiso.categoria ?? 'General',
                    permisos: []
                };
            }
        
            acc[idCategoria].permisos.push(permiso);
            return acc;
        
        }, {});
        
        area_permisos.html('');
        const arreglo_iconos = {
            SELECT: 'eye',
            CREATE: 'plus-circle',
            UPDATE: 'edit',
            CANCEL: 'trash-2'
        };

        Object.entries(permisosAgrupados).forEach(([idCategoria, categoria]) => {
        
            const collapseId = `collapse_categoria_${idCategoria}`;
        
            // Ordenar por bandera
            categoria.permisos.sort((a, b) => a.bandera - b.bandera);
        
            let permisosHTML = '';
        
                categoria.permisos.forEach(p => {
                    permisosHTML += `
                        <li class="list-group-item ps-4">
                            <div class="row">
                                <div class="col-6 col-md-8">
                                <i data-feather="${arreglo_iconos[p.tipo] ?? 'help-circle'}"></i>

                                    <strong>${p.permiso}</strong>
                                    <br>
                                    <small class="text-muted">${p.descripcion}</small>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="switch">
                                        <input type="checkbox"
                                            class="permiso-switch"
                                            data-permiso="${p.id}"
                                            ${p.estatus == 1 ? 'checked' : ''}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </li>

                        
                    `;
                });
        
            area_permisos.append(`
                <div class="col-12 mb-3">
                    <ul class="list-group list-group-flush">
        
                        <li class="list-group-item d-flex align-items-center py-2"
                            data-bs-toggle="collapse"
                            href="#${collapseId}"
                            role="button"
                            aria-expanded="false"
                            aria-controls="${collapseId}">
                            <i class="align-middle" data-feather="chevron-right"></i>
                            <strong class="fs-4">${categoria.nombre}</strong>
                        </li>
        
                        <div class="collapse" id="${collapseId}">
                            <div class="card card-body p-0">
                                <ul class="list-group list-group-flush">
                                    ${permisosHTML}
                                </ul>
                            </div>
                        </div>
        
                    </ul>
                </div>
            `);
        });
        feather.replace();

        }
      },
    });
  }

  function nuevoPermiso() {
    Swal.fire({
        title: 'Nuevo permiso',
        width: '600px', // Un poco más ancho para acomodar las columnas
        html: `
            <form id="form-nuevo-permiso" class="text-start mt-3">
                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label for="permiso_nombre" class="form-label fw-bold">Nombre del Permiso</label>
                        <input type="text" id="permiso_nombre" class="form-control" placeholder="Ej. Ver reportes">
                    </div>
                    <div class="col-12 col-md-6 mt-3 mt-md-0">
                        <label for="permiso_slug" class="form-label fw-bold">Slug (Identificador)</label>
                        <input type="text" id="permiso_slug" class="form-control" placeholder="Ej. ver_reportes">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="permiso_descripcion" class="form-label fw-bold">Descripción</label>
                    <textarea id="permiso_descripcion" class="form-control" rows="2" placeholder="Describe para qué sirve este permiso..."></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-md-4">
                        <label for="permiso_tipo" class="form-label fw-bold">Tipo</label>
                        <select id="permiso_tipo" class="form-select">
                            <option value="SELECT">SELECT (Ver)</option>
                            <option value="CREATE">CREATE (Crear)</option>
                            <option value="UPDATE">UPDATE (Editar)</option>
                            <option value="CANCEL">CANCEL (Eliminar)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0">
                        <label for="permiso_categoria" class="form-label fw-bold">Categoría</label>
                        <select id="permiso_categoria" class="form-select">
                            <option value="" selected disabled>Selecciona...</option>
                            <option value="1">Panel educativo</option>
                            <option value="2">Recibos de pago</option>
                            <option value="3">Catalogos</option>
                            <option value="4">Alumnos</option>
                            <option value="5">Profesores</option>
                            <option value="6">Materias</option>
                            <option value="7">Horarios</option>
                            <option value="8">Permisos</option>
                            <option value="9">Usuarios</option>
                            <option value="10">Escuelas</option>
                            <option value="11">Grupos</option>
                            <option value="12">Roles</option>
                            <option value="13">Gastos</option>
                            <option value="14">Asistencias</option>
                        </select>
                    </div>
                  
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar permiso',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            // Generador automático de slug mientras el usuario escribe el nombre
            const inputNombre = document.getElementById('permiso_nombre');
            const inputSlug = document.getElementById('permiso_slug');
            
            inputNombre.addEventListener('input', function() {
                let slug = this.value.toLowerCase().trim();
                slug = slug.replace(/[\s\W-]+/g, '_'); // Reemplaza espacios y caracteres raros por '_'
                inputSlug.value = slug;
            });
        },
        preConfirm: () => {
            // Extraer los valores cuando el usuario presiona "Guardar"
            const nombre = document.getElementById('permiso_nombre').value;
            const slug = document.getElementById('permiso_slug').value;
            const descripcion = document.getElementById('permiso_descripcion').value;
            const tipo = document.getElementById('permiso_tipo').value;
            const categoria = document.getElementById('permiso_categoria').value;

            // Validaciones básicas
            if (!nombre || !slug || !descripcion || !categoria) {
                Swal.showValidationMessage('Por favor, completa todos los campos requeridos');
                return false;
            }

            return {
                permiso: nombre,
                slug: slug,
                descripcion: descripcion,
                tipo: tipo,
                id_categoria: categoria,
                // Nota: 'estatus' lo defines en el backend por defecto como 1
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const dataFormulario = result.value;
            
            // Aquí haces tu fetch POST hacia el controlador
            console.log("Datos listos para enviar:", dataFormulario);
           $.ajax({
            type: "post",
            url: BASE_URL + "api/permisos.php?tipo=registrar_permiso",
            data: dataFormulario,
            dataType: "json",
            success: function (response) {
                if(response.estatus){
                    Swal.fire({
                        icon: 'success',
                        title: response.mensaje
                    })
                }else if(!response.estatus){
                    Swal.fire({
                        icon: 'error',
                        title: response.mensaje
                    })
                }
            }
           });
        }
    });
}
  window.nuevoPermiso = nuevoPermiso
});
