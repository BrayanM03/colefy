$(document).ready(function () {
    reloadTable()
    
  });

  import { initCustomDataTable } from '../DataTable/datatables-init.js';
  import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
  let table_prehorario;

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
  
  function convertirHora(hora24) {
    const [hora, minuto] = hora24.split(':');
    const horas = parseInt(hora, 10);
    const periodo = horas >= 12 ? 'pm' : 'am';
    const hora12 = horas % 12 || 12; // Convierte 0 y 12 a 12 en formato de 12 horas
    return `${hora12}:${minuto} ${periodo}`;
}

  function reloadTable(){
    const role = $('#role').attr("role");
    const columns = [
        { data:'id', title:'#' },
        { data:'profesor', title:'Profesor'},
        { data:'materia', title:'Materia' }, 
        { data:'dia', title:'Día' }, 
        { data:'hora', title:'Hora', render:(data) =>{
          return convertirHora(data);
        }}, 
        {data:null, render: function(row){
          return `
          <div class='row'>
                      <div class='col-12 col-md-12'> 
                         <div class="btn btn-danger btn-eliminar-prehorario"><i class="fa-solid fa-trash"></i></div>
                      </div>
                  </div>
          `;
        }}
    ];
    table_prehorario = initCustomDataTable('#tabla-prehorario', BASE_URL + 'api/horarios.php?tipo=prehorarios', columns);
    DataTableListener(table_prehorario, 'click', '.btn-eliminar-prehorario', eliminarPrehorario)
  }

  function agregarPrehorario(){
    let validacion = validarFormulario();
    if(!validacion['estatus']){
      Toast.fire({
        icon: "error",
        title: validacion['mensaje']
      });
    }else{

      let profesor = $("#profesor").val();
      let materia = $("#materia").val();
      let dia = $("#dia").val();
      let hora = $("#hora").val();
     
      $.ajax({ 
        type: "post",
        url: BASE_URL + 'servidor/horarios/agregar-detalle-prehorario.php',
        data: {profesor, materia, dia, hora},
        dataType: "json",
        success: function (response) {
          if(response.estatus){
       
           table_prehorario.ajax.reload(null, false)
            Toast.fire({
              icon: "success",
              title: response['mensaje']
            });
          }else{
            Toast.fire({
              icon: "error",
              title: response['mensaje']
            });
          }
          
        }
      });

      
    }
  }

  function validarFormulario(){
    let arreglo_res;
      let profesor = $("#profesor").val();
      let materia = $("#materia").val();
      let dia = $("#dia").val();
      let hora = $("#hora").val();
      if(profesor.trim()==''){
        arreglo_res = {'estatus': false, 'mensaje': 'Selecciona un profesor'}
      }

      else if(materia.trim()==''){
        arreglo_res = {'estatus': false, 'mensaje': 'Selecciona una materia'}
      }
      
      else if(dia.length ==0){
        arreglo_res = {'estatus': false, 'mensaje': 'Selecciona un día'}
      }

      else if(hora.trim()==''){
        arreglo_res = {'estatus': false, 'mensaje': 'Selecciona una hora'}
      }else{
        arreglo_res = {'estatus': true, 'mensaje': 'Agregado correctamente'}
      }
   
      return arreglo_res
  }

  function eliminarPrehorario(id){
      $.ajax({
        type: "POST",
        url: BASE_URL + "api/horarios.php?tipo=eliminar_prehorario&id_prehorario="+id,
        data: {},
        dataType: "json",
        success: function (response) {
          if(response.estatus){
            table_prehorario.ajax.reload(null, false);
            Toast.fire({
              icon: "success",
              title: response['mensaje']
            });
          }else{
            Toast.fire({
              icon: "error",
              title: response['mensaje']
            });
          }
        }
      });
  }

  function registrarHorario(){
    let nombre_horario = $("#nombre-horario").val();
    if(nombre_horario.trim() ==''){
      Toast.fire({ 
        icon: "error",
        title: 'Escribe un nombre para el horario'
      });
      return false;
    }

    $.ajax({
      type: "post",
      url: BASE_URL + "api/horarios.php?tipo=registrar_horario&nombre=" + nombre_horario,
      data: {},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          Swal.fire({
            icon: 'success',
            title: response.mensaje,
            confirmButtonText: 'Entendido'
          }).then(()=>{
            window.location.reload()
          })
        }else{
          Swal.fire({
            icon: 'error',
            title: response.mensaje,
            confirmButtonText: 'Entendido'
          }).then(()=>{
            window.location.reload()
          })
        }

      }
    });
  }

  //Listeners
  GeneralEventListener('btn-agregar-prehorario', 'click', agregarPrehorario)
  GeneralEventListener('btn-registrar-horario', 'click', registrarHorario)
