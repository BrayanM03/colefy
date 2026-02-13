
import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
let table_escuelas;

$(document).ready(function () {
  const role = $('#role').attr("role");
  const columns = [
    { data: 'id', title: '#' },
    {data: 'nombre', title: 'Nombre' },
    {data: 'direccion', title: 'Dirección'},
    { data: 'cedula', title: 'Cedula' },
    { data: 'telefono', title: 'Teléfono' },
    { data: 'fecha_registro', title: 'Fecha reg.' },
    { data: 'fecha_pago', title: 'Prox. pago' },
    { data: null, title: 'Estatus' , render: (data)=>{
      let estatus_tag=''
      if(data.estatus ==1){
        estatus_tag = '<span class="badge bg-success">Activo</span>'
      }else{
        estatus_tag = '<span class="badge bg-secondary">Inactivo</span>'
      }
      return estatus_tag;
    }},
    {
      data: null, title: 'Opciones', render: function (data, type, row) {
      
       /*  if (role == 1) { */
          return `
            <div class='row'>
              <div class='col-12 col-md-12'>
                <div class="btn btn-primary btn-editar-alumno">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div class="btn btn-danger btn-cancelar-alumno">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
       /*  } else {
          return '';
        } */
      }
    }
  ];

  table_escuelas = initCustomDataTable('#tabla-escuelas', BASE_URL + 'api/escuelas.php?tipo=datatable', columns);

  DataTableListener(table_escuelas, 'click', '.btn-editar-alumno', editarAlumno);
  DataTableListener(table_escuelas, 'click', '.btn-cancelar-alumno', cancelarAlumno);
  GeneralEventListener('btn-registrar-alumno', 'click', registrarAlumno);
});

function registrarAlumno(){
  Swal.fire({
    title: 'Agregar alumno',
    html:`
    <div class="container">
      <div class="row mb-3">
            <div class="col-12">
                <label for="nombre">Nombre</label>
                <input class="form-control" id="nombre" type="text" placeholder="Nombre...">
            </div>
      </div>
      <div class="row mb-3">
            <div class="col-6">
                <label for="apellido_paterno">Apellido Materno</label>
                <input id="apellido_paterno"class="form-control" type="text" placeholder="Apellido paterno..">
            </div>
            <div class="col-6">
              <label for="apellido_materno">Apellido materno</label>
              <input id="apellido_materno" class="form-control" type="text" placeholder="Apellido materno...">
          </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="cumple">Cumpleaños</label>
            <input id="cumple" class="form-control" type="date">
        </div>
        <div class="col-6">
          <label for="genero">Genero</label>
          <select id="genero" class="form-control">
              <option value="M">Masculino</option>
              <option value="F">Femenino</option>
          </select>
        </div>
    </div>
    <div class="row">
    <div class="col-6">
        <label for="telefono">Teléfono</label>
        <input id="telefono" class="form-control" type="text" placeholder="+52 8681...">
    </div>
    <!----<div class="col-6">
        <label for="grupo">Grupo</label>
        <select id="grupo" class="form-control selectpicker" placeholder="Selecciona un grupo">
        </select>
    </div>-->
   
</div>
    </div>
    `,
    didOpen:()=>{
    /*   $.ajax({
        type: "post",
        url: "../api/grupos.php?tipo=combo",
        data: "data",
        dataType: "json",
        success: function (response) {
          $("#grupo").empty()
          $("#grupo").append(`<option>Selecciona un grupo</option>`)
          if(response.estatus){
            response.data.forEach(element => {

               $("#grupo").append(`
                  
                  <option value="${element.id}">${element.nombre}</option>
               `)
            });
          }else{
            $("#grupo").append(`
                 
                  <option value="">${response.mensaje}</option>
               `)
          }
        }
      }); */
    }, 
    confirmButtonText: 'Registrar',
    showCloseButton: true,
    preConfirm: (value) => {
      //Validación
      let nombre = $("#nombre").val()
      let apellido_paterno = $("#apellido_paterno").val()
      let apellido_materno = $("#apellido_materno").val()
      let grupo = $("#grupo").val()

      if(!nombre){
        Swal.showValidationMessage('Escribe un nombre')
      }else if(!apellido_paterno){
        Swal.showValidationMessage('Escribe un apellido paterno')

      }/* else if(!grupo){
        Swal.showValidationMessage('Selecciona un grupo porfavor, si no hay opciones debes crear uno antes de añadir alumnos')
      } */
    }

  }).then((r)=>{
    if(r.isConfirmed){
      let nombre = $("#nombre").val()
      let apellido_paterno = $("#apellido_paterno").val()
      let apellido_materno = $("#apellido_materno").val()
      let fecha = $("#cumple").val()
      let genero = $("#genero").val()
      let telefono = $("#telefono").val()

      $.ajax({
        type: "POST",
        url: BASE_URL +"api/alumnos.php?tipo=registrar",
        data: {nombre, apellido_paterno, apellido_materno, 'cumple':fecha, genero, telefono},
        dataType: "JSON",
        success: function (response) {
          table_escuela.ajax.reload(null, false)
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
                      table_escuelas.ajax.reload(false)

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
                      table_escuelas.ajax.reload(false)


                    }
                })
            }

            
        }
    });
    }
  })
}

function cancelarAlumno(id_alumno){
  Swal.fire({
    icon: 'question',
    title: '¿Deseas desactivar este alumno?',
    showCancelButton:true,
    cancelButtonText: 'No',
    confirmButtonText:'Si',
    showCloseButton: true
  }).then(r=>{
    if(r.isConfirmed){
      $.ajax({
        type: "POST",
        url: BASE_URL +"servidor/historial/eliminar-registro.php",
        data: {'id_reg':id_alumno, 'tabla': 'alumnos'},
        dataType: "JSON",
        success: function (response) {
          table_escuelas.ajax.reload(null, false);
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

function editarAlumno(id_alumno){
  Swal.fire({
    title: 'Editar alumno',
    html:`
    <div class="container">
      <div class="row mb-3">
            <div class="col-12">
                <label for="nombre">Nombre</label>
                <input class="form-control" id="nombre" type="text" placeholder="Nombre...">
            </div>
      </div>
      <div class="row mb-3">
            <div class="col-6">
                <label for="apellido_paterno">Apellido Materno</label>
                <input id="apellido_paterno"class="form-control" type="text" placeholder="Apellido paterno..">
            </div>
            <div class="col-6">
              <label for="apellido_materno">Apellido materno</label>
              <input id="apellido_materno" class="form-control" type="text" placeholder="Apellido materno...">
          </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="cumple">Cumpleaños</label>
            <input id="cumple" class="form-control" type="date">
        </div>
        <div class="col-6">
          <label for="genero">Genero</label>
          <select id="genero" class="form-control">
              <option value="M">Masculino</option>
              <option value="F">Femenino</option>
          </select>
        </div>
    </div>
    <div class="row">
    <div class="col-6">
        <label for="telefono">Teléfono</label>
        <input id="telefono" class="form-control" type="text" placeholder="+52 8681...">
    </div>
    <!----<div class="col-6">
        <label for="grupo">Grupo</label>
        <select id="grupo" class="form-control selectpicker" placeholder="Selecciona un grupo">
        </select>
    </div>-->
   
</div>
    </div>
    `,
    didOpen:()=>{
     $.ajax({
      type: "post",
      url: BASE_URL + "api/alumnos.php?tipo=traer",
      data: {id_alumno},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          response.data.forEach(element => {
             $("#nombre").val(element.nombre)
             $("#apellido_paterno").val(element.apellido_paterno)
             $("#apellido_materno").val(element.apellido_materno)
             $("#cumple").val(element.fecha_cumple)
             $("#genero").val(element.genero)
             $("#telefono").val(element.telefono)
          });
        }else{
          alert('No se encontró el alumno');
        }
      }
    });
    }, 
    confirmButtonText: 'Actualizar',
    showCloseButton: true,
    preConfirm: (value) => {
      //Validación
      let nombre = $("#nombre").val()
      let apellido_paterno = $("#apellido_paterno").val()
   
      if(!nombre){
        Swal.showValidationMessage('Escribe un nombre')
      }else if(!apellido_paterno){
        Swal.showValidationMessage('Escribe un apellido paterno')
      }
    }

  }).then((r)=>{
    if(r.isConfirmed){
      let nombre = $("#nombre").val()
      let apellido_paterno = $("#apellido_paterno").val()
      let apellido_materno = $("#apellido_materno").val()
      let fecha = $("#cumple").val()
      let genero = $("#genero").val()
      let telefono = $("#telefono").val()

      $.ajax({
        type: "POST",
        url: BASE_URL + "api/alumnos.php?tipo=actualizar",
        data: {id_alumno, nombre, apellido_paterno, apellido_materno, 'cumple':fecha, genero, telefono},
        dataType: "JSON",
        success: function (response) {
          table_escuelas.ajax.reload(null, false)
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





