const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  }
});

import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
let table;

reloadTable()
function reloadTable(){
  const role = $('#role').attr("role");

  const columns = [
    { data: 'id', title: '#' },
    {data: 'nombre', title: 'Nombre'},
    {data: null, title: 'Apellidos', render:(data)=>{
        return data.apellido_paterno + ' ' + data.apellido_materno
    }},
    {
      data: null, title: 'Opciones', render: function (data, type, row) {
        return `
        <div class='row'>
          <div class='col-12 col-md-12'>
            <div class="btn btn-danger btn-eliminar-alumno">
              <i class="fa-solid fa-trash"></i>
            </div>
          </div>
        </div>`;
      }
    }
  ];

  table = initCustomDataTable('#detalle_pregrupo', BASE_URL + 'api/grupos.php?tipo=datatable_pregrupo', columns)
  DataTableListener(table, 'click', '.btn-eliminar-alumno', eliminarAlumnoPreGrupo)

}

function registrarGrupo(){
    let nombre = $("#nombre-horario").val()
    let grado = $("#grado").val()
    let nivel = $("#nivel").val()
    let ciclo = $("#ciclo").val()

    if(!nombre){
      Toast.fire({
        icon: "error",
        title: 'Escribe un nombre para el grupo'
      });
    }
    if(!grado){
      Toast.fire({
        icon: "error",
        title: 'Seleccione un grado'
      });
    }
    if(!nivel){
      Toast.fire({
        icon: "error",
        title: 'Selecciona un nivel'
      });
    }

    $.ajax({
      type: "post",
      url: BASE_URL + "api/grupos.php?tipo=registrar",
      data: {nombre, nivel, grado, ciclo},
      dataType: "json",
      success: function (response) {
        table.ajax.reload(null, false)
        if(response.estatus){
          Swal.fire({
            icon: "success",
            title: response.mensaje,
            confirmButtonText: 'Entendido',
            showCloseButton: true,
            html: `<h4 style="color:rgba(100, 150, 200)">${response.subtitulo}</h4>`

          });
          $("#nombre-horario").val('')
          $("#grado").val('')
          $("#nivel").val('')
          $("#ciclo").val('')

        }else{
          Swal.fire({
            icon: "error",
            title: response.mensaje,
            confirmButtonText: 'Entendido',
            showCloseButton: true,
            html: `<h4 style="color:rgba(100, 150, 200)">${response.subtitulo}</h4>`
          });
        }
      }
    });
  }


  let debounceTimer;

  $('#alumno').on('shown.bs.select', function () {
    let $input = $('.bs-searchbox input'); // Input de búsqueda interno
  
    $input.off('keyup.miEvento').on('keyup.miEvento', function () {
      clearTimeout(debounceTimer); // Cancelar timeout anterior
  

      const value = $(this).val().trim();
    if (!value) return; // si no hay texto, no hacemos nada

      debounceTimer = setTimeout(() => {
        console.log('Buscando:', value);
        // Aquí llamas tu función para hacer la petición AJAX
        buscarAlumno(value);
      }, 600); // Espera 600ms desde la última tecla
    });
  });

  function buscarAlumno(busqueda){
    $.ajax({
      type: "post",
      url: BASE_URL + "api/alumnos.php?tipo=combo",
      data: {busqueda},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          console.log(response);
          $("#alumno").empty()
          response.data.forEach(element => {
            $("#alumno").append(`<option value="${element.id}">${element.nombre +' '+ element.apellido_paterno+' '+element.apellido_materno }</option>`)
          });
          $('#alumno').selectpicker('refresh');

        }
      }
    });
  }

  function registrarAlumnoPreGrupo(){
    let alumno = $("#alumno").val()
    $.ajax({
      type: "post",
      url: BASE_URL + "api/grupos.php?tipo=preregistrar",
      data: {alumno},
      dataType: "json",
      success: function (response) {
        table.ajax.reload(null, false)
        if(response.estatus){
          Toast.fire({
            icon: "success",
            title: response.mensaje
          });
        }else{
          Toast.fire({
            icon: "error",
            title: response.mensaje
          });
        }
      }
    });
  }

  function eliminarAlumnoPreGrupo(id_detalle){
    $.ajax({
      type: "post",
      url: BASE_URL + "api/grupos.php?tipo=eliminar_detalle_pregrupo",
      data: {id_detalle},
      dataType: "json",
      success: function (response) {
        table.ajax.reload(null, false)
        if(response.estatus){
          Toast.fire({
            icon: "success",
            title: response.mensaje,
          
          });
        }else{
          Toast.fire({
            icon: "error",
            title: response.mensaje,
          
          });
        }
      }
    });
  }

  GeneralEventListener('btn-registrar-alumno-pregrupo', 'click', registrarAlumnoPreGrupo)
  GeneralEventListener('btn-registrar-grupo', 'click', registrarGrupo)
  