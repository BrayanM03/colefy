import { GeneralEventListener } from "../utils/listeners.js";
const id_rol = ID_ROL
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
      url: BASE_URL+"/api/permisos.php?tipo=ver_lista_permisos_x_rol",
      data: {id_rol},
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
                                            ${p.activo == 1 ? 'checked' : ''}>
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
});


$(document).on('change', '.permiso-switch', function() {
    const $input = $(this);
    const id_permiso = $input.data('permiso');
    const activo = $input.is(':checked') ? 1 : 0;
  
    $.ajax({
        type: "post",
        url: BASE_URL+"api/permisos.php?tipo=gestion_permiso_rol",
        data: {
            accion: 'actualizar',
            id_rol,
            id_permiso,
            valor: activo
        },
        dataType: "json",
        success: function (response) {
          
            if (response.estatus) {
                Toast.fire({icon:'success', 'title': response.mensaje})
                // Notificación opcional (puedes usar Toastr o SweetAlert)
            } else {
                Toast.fire({icon:'warning', 'title': response.mensaje})
            }
        },
        error: function() {
            alert("Error de conexión con el servidor");
        }
    });
});


const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })


  