import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {GeneralEventListener} from '../utils/listeners.js';

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
   /* {
      data: null, title: 'Ciudad', render: function (data, type, row) {
        return row['ciudad'] + ', ' + row['estado'];
      }
    }, */
   /*  { data: 'telefono', title: 'Teléfono' },
    { data: 'correo', title: 'Correo' }, */
    {
      data: null, title: 'Opciones', render: function (data, type, row) {
        if (role == 1) {
          return `
            <div class='row'>
              <div class='col-12 col-md-12'>
                <div class="btn btn-primary" onclick="editarSolicitud(${row.id}, false)">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div class="btn btn-danger" onclick="cancelarUsuario(${row.id})">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
        } else {
          return '';
        }
      }
    }
  ];

  table = initCustomDataTable('#example', BASE_URL + 'api/profesores.php?tipo=datatable', columns);

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
    console.log(r);
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

function cancelarUsuario(id_usuario){
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


