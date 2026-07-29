import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {GeneralEventListener} from '../utils/listeners.js';
import {tienePermiso, enlazarLicencia} from '../utils/permisos.js';

let table;
$(document).ready(function () {
  const role = $('#role').attr("role");
  let estatus_tag;
  const columns = [
    { data: 'id', title: '#' },
    {
      data: null, title: 'Nombre', render: function (data, type, row) {
        return row['nombre'] + ' ' + row['apellido'];
      }
    },
     { data: 'telefono', title: 'Telefono' },
     { data: 'correo', title: 'Correo' },
     { data: null, title: 'Estatus' , render: (data)=>{
      if(data.estatus ==1){
        estatus_tag = '<span class="badge bg-success">Activo</span>'
      }else{
        estatus_tag = '<span class="badge bg-secondary">Inactivo</span>'
      }
      return estatus_tag;
    }},
    { data: null, title: 'Licencia' , render: (data)=>{
      if(data.id_usuario != null){
        estatus_tag = '<span class="badge bg-info">Con Licencia</span>'
      }else{
        estatus_tag = '<span class="badge bg-warning">Sin licencia</span>'
      }
      return estatus_tag;
    }},

    {
      data: null, title: 'Opciones', render: function (data, type, row) {
        var btn_editar ='';
          var btn_cancelar='';
          var btn_enlazar ='';
        if (tienePermiso('editar_profesores')) {
         btn_editar = `
                <div class="btn btn-primary" onclick="editarProfesor(${row.id}, false)">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div>`;
        }
        if(tienePermiso('cancelar_profesores')){
         btn_cancelar = `
          <div class="btn btn-danger" onclick="cancelarProfesor${row.id})">
          <i class="fa-solid fa-trash"></i>
        </div>`
        }
        if(tienePermiso('enlazar_licencia')){
          btn_enlazar = `
           <div class="btn btn-info" onclick="enlazarLicencia(${row.id}, ${row.id_usuario}, '${row.usuario}')">
           <i class="fa-solid fa-circle-nodes"></i>
         </div>`
         }

        return `
        <div class='row'>
          <div class='col-12 col-md-12'>
            ${btn_editar}${btn_cancelar} ${btn_enlazar}
          </div>
        </div>`;
      }
    }
  ];

  table = initCustomDataTable('#example', BASE_URL + 'api/profesores.php?tipo=datatable', columns);
 
   // Escuchar el evento que viene del otro archivo
    document.addEventListener('licenciaActualizada', function() {
      table.ajax.reload(null, false);
  });
  GeneralEventListener('registrar-profesor', 'click', registrarProfesor)
});

const container = document.getElementById('profesores-container');
if (container && container.dataset.autoOpen === 'true') {
  registrarProfesor()
}

function registrarProfesor(){
  Swal.fire({
    title: 'Agregar profesor', 
    html:`
     <div class="container">
        <div class="row mb-3">
        <div class="col-12" style="border: 1px solid gray; background-color: whitesmoke; border-radius: 7px; padding: 1rem;">
            <span style="font-size:13px; color: gray;">Para que un profesor tenga un usuario en el sistema debe enlazarse a una licencia, contacte al admin para mas información</span>
        </div>
     </div>
      <div class="row mb-3">
            <div class="col-12">
                <label for="nombre">Nombre</label>
                <input class="form-control" id="nombre" type="text" placeholder="Nombre...">
            </div>
      </div>
      <div class="row mb-3">
            <div class="col-12">
                <label for="apellidos">Apellidos</label>
                <input id="apellidos"class="form-control" type="text" placeholder="Apellidos..">
            </div>   
      </div>
      <div class="row mb-3">
            <div class="col-6">
                <label for="especialidad">Especialidad</label>
                <input id="especialidad"class="form-control" type="text" placeholder="Ingles, Matematicas etc..">
            </div>  
            <div class="col-6">
              <label for="telefono">Teléfono</label>
              <input id="telefono" class="form-control" type="text" placeholder="+52 8681...">
            </div> 
      </div>

    </div>
    `,
    didOpen:()=>{}, 
    confirmButtonText: 'Registrar',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    showCloseButton: true,
    preConfirm: (value) => {
      //Validación
      let nombre = $("#nombre").val()
      let apellidos = $("#apellidos").val()

      if(!nombre){
        Swal.showValidationMessage('Escribe un nombre')
      }else if(!apellidos){
        Swal.showValidationMessage('Escribe los apellidos')
      }
    }

  }).then((r)=>{
    if(r.isConfirmed){
      let nombre = $("#nombre").val()
      let apellidos = $("#apellidos").val()
      let especialidad = $("#especialidad").val()
      let telefono = $("#telefono").val()

      $.ajax({
        type: "POST",
        url: BASE_URL +"api/profesores.php?tipo=registrar",
        data: {nombre, apellidos, especialidad, telefono},
        dataType: "JSON",
        success: function (response) {
          table.ajax.reload(null, false)
            if(response.estatus == true){
                Swal.fire({
                    icon: 'success',
                    html: `
                    ${response.mensaje}<br>
                    `,
                    allowOutsideClick: true,
                    confirmButtonText: "Entendido",
                    showCancelButton: false,
                    
                }).then((r)=>{
                    if(r.isConfirmed){
                      table.ajax.reload(false)

                    }
                })

            }else{
                Swal.fire({
                    icon: 'error',
                    html: `
                    Ocurrio un error: ${response.mensaje}
                    `,
                    allowOutsideClick: true,
                    showCancelButton: false,
                    confirmButtonText: "Entendido",

                    
                }).then((r)=>{
                    if(r.isConfirmed){
                      table.ajax.reload(false)


                    }
                })
            }

            
        }
    });
    }
  })
}

function cancelarProfesor(id_usuario){
  Swal.fire({
    icon: 'question',
    title: '¿Deseas desactivar este usuario?',
    showCancelButton:true,
    cancelButtonText: 'No',
    confirmButtonText:'Si',
    showCloseButton: true
  }).then(r=>{
    if(r.isConfirmed){
      $.ajax({
        type: "POST",
        url: BASE_URL + "servidor/historial/eliminar-registro.php",
        data: {'id_reg':id_usuario, 'tabla': 'usuarios'},
        dataType: "JSON",
        success: function (response) {
            if(response.estatus == true){
                Swal.fire({
                    icon: 'success',
                    html: `
                    ${response.mensaje}<br>
                    `,
                    allowOutsideClick: true,
                    confirmButtonText: "Entendido",
                    showCancelButton: false,
                    
                }).then((r)=>{
                    if(r.isConfirmed){
                        tabla.ajax.reload(false)

                    }
                })

            }else{
                Swal.fire({
                    icon: 'error',
                    html: `
                    Ocurrio un error: ${response.mensaje}
                    `,
                    allowOutsideClick: true,
                    showCancelButton: false,
                    confirmButtonText: "Entendido",

                    
                }).then((r)=>{
                    if(r.isConfirmed){
                        tabla.ajax.reload(false)


                    }
                })
            }

            
        }
    });
    }
  })
}

window.enlazarLicencia = enlazarLicencia



