import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';


cargarPermisosUsuario() 
GeneralEventListener('btn-agregar-permiso', 'click', modalAgregarPermisoUsuario)

let id_usuario = ID_USER;
 
// Función genérica para la petición AJAX
function ejecutarAjaxPermiso(datos) {
    $.ajax({
        type: "post",
        url: BASE_URL + "./api/permisos.php?tipo=gestion_permiso_individual",
        data: datos,
        dataType: "json",
        success: function (response) {
          
            if (response.estatus) {
                Toast.fire({icon:'success', 'title': response.mensaje})
                // Notificación opcional (puedes usar Toastr o SweetAlert)
                
                // IMPORTANTE: Recargamos la vista para que los colores se actualicen
                cargarPermisoIndividual(response.data, datos.accion);
                //cargarPermisosUsuario(); 
            } else {
                Toast.fire({icon:'warning', 'title': response.mensaje})
            }
        },
        error: function() {
            alert("Error de conexión con el servidor");
        }
    });
}


function cargarPermisosUsuario() {                                                                                      
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
        url: BASE_URL + "api/permisos.php?tipo=permisos_usuario",
        data: {id_usuario},
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
                                               //Gris      //Azul
                  let bg_switch = (p.valor_rol == null) ? '#47BAB6' : '#51AFF7'
                  bg_switch = (p.valor_user == null) ? bg_switch : '#48D1CC' //Verde
                  let rol_tag = (p.rol== null) ? '' : p.rol;
                  let top_hereditary_tag = (p.rol== null) ? 'No tiene permiso' : 'Hereda de: ';
                 // 1. Caso Rojo: Usuario 0 y Rol 1
                 let statusClass=''
                 let checked_switch = ''
                 let btn_display ='';
                if (p.valor_usuario == 0 && p.valor_rol == 1) {
                    statusClass = 'switch-rojo';
                    bg_switch = 'tomato'
                    top_hereditary_tag = 'Permiso den.'
                    rol_tag = 'Denegado';
                    checked_switch = ''
                } 
                // 2. Casos Azul: (Usuario 1 y Rol 1) O (Usuario NULL y Rol 1)
                else if (p.valor_rol == 1) {
                    statusClass = 'switch-azul';
                    bg_switch ='#51AFF7';
                    checked_switch = 'checked'
                    btn_display ='d-none'

                } 
                // 3. Casos Verde: (Usuario 1 y Rol 0) O (Usuario 1 y Rol NULL)
                else if (p.valor_usuario == 1) {
                    statusClass = 'switch-verde';
                    bg_switch = '#48D1CC';
                    top_hereditary_tag = 'Permiso esp.'
                    rol_tag = 'Concedido';
                    checked_switch = 'checked'
                } 
                // 4. Todos los demás casos (0/0, null/null, 0/null, null/0) -> Gris
                else {
                    statusClass = 'switch-gris';
                    btn_display ='d-none'
                }
                  permisosHTML += `
                      <li class="list-group-item ps-4">
                          <div class="row">
                              <div class="col-6 col-md-7">
                               <i data-feather="${arreglo_iconos[p.tipo] ?? 'help-circle'}"></i>
  
                                  <strong>${p.permiso}</strong>
                                  <br>
                                  <small class="text-muted">${p.descripcion}</small>
                              </div>
                              <div class="col-6 col-md-5">
                                  <div class="row">
                                        <div class="col-md-3 col-12">
                                            <label id="l${p.id}" class="switch ${statusClass} mr-2 mt-2">
                                                <input type="checkbox"
                                                    class="permiso-switch" 
                                                    id="p${p.id}"
                                                    data-idreg="${p.id_registro_pu}"
                                                    data-permiso="${p.id}"
                                                    ${checked_switch}>
                                                <span class="slider" id="s${p.id}"></span>
                                            </label>
                                        </diV>
                                        <div id="tag_${p.id}" class="col-md-3 col-12">
                                            <span style="font-size:12px" class="text-center">${top_hereditary_tag}</span><br>
                                            <h5 class="text-center"><span class="badge" style="background-color:${bg_switch};">${rol_tag}</span></h5>   
                                        </diV>
                                        <div id="btn_reset_${p.id}" class="col-md-4 col-12">
                                            <div data-permiso="${p.id}" class="btn mt-1 ${btn_display} btn-reset-permiso" style="margin-left:1rem !important; background-color:tomato; color: white;"><i class="fa-solid fa-rotate-left"></i></div>
                                        </diV>
                                  </div>
                                </div>
                          </div>
                      </li>
                  `;
                  if(p.valor_rol == 1){
                    let input_check = document.getElementById('p'+p.id)
                  }
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
  
          }else{
            area_permisos.empty();
            area_permisos.append(`
            <div class="col-12 mb-3 text-center m-auto">
                    <div>
                     <img src="./img/empty.png" style="width: 80px">
                     <span style="color: gray; margin-top: 3rem;">Sin permisos encontrados<span>
                     </div>
            </div>          
            
            `)
          }
        },
      });
    }
  };
  
function modalAgregarPermisoUsuario(){
    let permisos_generales 
    $.ajax({
        type: "post",
        url: BASE_URL + "./api/permisos.php?tipo=ver_lista_permisos_faltantes",
        data: {id_usuario},
        dataType: "json",
        success: function (response) {
          if (response.estatus) {
            Swal.fire({
                title: 'Agregar permiso',
                html: `
                <div class="container">
                    <div class="row">
                        <div class="col-12" id="contenedor-agregar-permiso">
                            <label for="ids_permisos">Permiso</label>
                            <select id="ids_permisos"
                                    class="form-control"
                                    multiple>
                            </select>
                        </div>
                    </div>
                </div>
                `,
                didOpen: ()=>{
                    
                    if(response.estatus){
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
                        
                        const $permiso = $('#ids_permisos');
                       // $permiso.empty()
                        
                        Object.entries(permisosAgrupados).forEach(([idCategoria, categoria]) => {
                            let permisosHTML='';
                            categoria.permisos.sort((a, b) => a.bandera - b.bandera);
                            categoria.permisos.forEach(p => {
                                permisosHTML += `
                                   <option value="${p.id}">${p.descripcion}</option> 
                                `;
                            });   
                            $('#ids_permisos').append(`
                            <optgroup label="${categoria.nombre}">
                            ${permisosHTML}
                            </optgroup>
                            `);
                        })  
                        $permiso.selectpicker({
                            container: '.swal2-popup',
                            liveSearch: true,
                            actionsBox: true,
                            noneSelectedText: 'Seleccione un permiso'})
                    }
                },
                confirmButtonText: 'Registrar',
                showCloseButton: true
            }).then((r)=>{
                if(r.isConfirmed){
                    let ids_permisos = $("#ids_permisos").val()
                    agregarPermisoUsuario(ids_permisos)
                
            }})

          }}
        })


    
}

function agregarPermisoUsuario(ids_permisos){
    $.ajax({
        type: "post",
        url: BASE_URL + "./api/permisos.php?tipo=agregar_permiso_usuario",
        data: {ids_permisos, id_usuario},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                Swal.fire({
                    icon: 'success',
                    title: 'Se insertaron permisos correctamente'
                })

                cargarPermisosUsuario() 

            }
        }
    });
}

function cargarPermisoIndividual(data, accion){
    let id_permiso = data.id_permiso
    let switch_color = data.switch

    let color_bg = '#ced4da';
    let tag_permiso = $("#tag_"+id_permiso);
    let btn_reset = $("#btn_reset_"+id_permiso);
    let top_hereditary_tag ='No tiene permiso'
    let rol_tag = ''
    let btn_display ='d-none'
    if(switch_color==1){
        color_bg = '#48D1CC'
        top_hereditary_tag = 'Permiso esp.'
        rol_tag = 'Concedido'
        btn_display =''
    }else if(switch_color ==2){
        color_bg = '#51AFF7'
        top_hereditary_tag = 'Hereda de'
        rol_tag = data.nombre_rol
        btn_display ='d-none'

        if(accion=='actualizar'){
            $("#l"+id_permiso).removeClass('switch-rojo')
        }


    }else if(switch_color == 3){
        color_bg = 'tomato'
        top_hereditary_tag = 'Permiso den.'
        rol_tag = 'Denegado'
        btn_display =''

    }

    if(switch_color==0 && accion == 'reset'){
        document.getElementById("p"+id_permiso).checked = false
    }

    if(switch_color==2 && accion == 'reset'){
        document.getElementById("p"+id_permiso).checked = true
        btn_display ='d-none'
    }
    tag_permiso.empty().append(`
    <span style="font-size:12px" class="text-center">${top_hereditary_tag}</span><br>
    <h5 class="text-center"><span class="badge" style="background-color:${color_bg};">${rol_tag}</span></h5>   
    `)
    btn_reset.empty().append(`
    <div data-permiso="${id_permiso}" class="btn mt-1 ${btn_display} btn-reset-permiso" style="margin-left:1rem !important; background-color:tomato; color: white;"><i class="fa-solid fa-rotate-left"></i></div>
    `)
    $("#s"+id_permiso).css('background-color', color_bg)
   

};

// 1. Evento cuando cambian el Switch (Activar/Denegar)
$(document).on('change', '.permiso-switch', function() {
    const $input = $(this);
    const id_permiso = $input.data('permiso');
    const id_registro = $input.data('idreg');
    const activo = $input.is(':checked') ? 1 : 0;
    
    ejecutarAjaxPermiso({
        accion: 'actualizar',
        id_usuario: id_usuario,
        id_permiso: id_permiso,
        valor: activo
    });
});

// 2. Evento cuando presionan el botón de Reset (Volver al heredado)
$(document).on('click', '.btn-reset-permiso', function() {
    const id_permiso = $(this).data('permiso');

    if(confirm('¿Deseas restablecer este permiso y heredar el valor del rol?')) {
        ejecutarAjaxPermiso({
            accion: 'reset',
            id_usuario: id_usuario,
            id_permiso: id_permiso
        });
    }
});

/* function guardarPermisosUsuario() { 
    const urlParams = new URLSearchParams(window.location.search);
    const id_usuario = urlParams.get('id_usuario'); 
    const permisos = [];

    document.querySelectorAll('.permiso-switch').forEach(input => {
        permisos.push({
            id: input.dataset.id,
            activo: input.checked ? 1 : 0
        });
    });

    $.ajax({
        type: "post",
        url: "../api/permisos.php?tipo=guardar_permisos_usuario",
        data: {id_usuario, permisos},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                Toast.fire({icon:'success', 'title': response.mensaje})
            }else{
                Toast.fire({icon:'warning', 'title': response.mensaje})

            }
        }
    });
 } */

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


  