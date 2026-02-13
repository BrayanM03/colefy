$(document).ready(function () {
    reloadTable()
    
  });
  import { initCustomDataTable } from '../DataTable/datatables-init.js';
  import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
  let table;

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

function reloadTable(){
    const role = $('#role').attr("role");
    const columns = [
        { data:'id', title:'#' },
        { data:'grupo', title:'Grupo'},
        { data:'horario', title:'Horario' }, 
        {data:null, render: function(row){
          return `
          <div class='row'>
                      <div class='col-12 col-md-12'> 
                         <div class="btn btn-danger btn-cancelar-asignacion"><i class="fa-solid fa-trash"></i></div>
                      </div>
                  </div>
          `;
        }}
    ];
    table = initCustomDataTable('#grupos-horarios', BASE_URL + 'api/horarios.php?tipo=tabla_grupos_horario', columns);
    DataTableListener(table, 'click', '.btn-cancelar-asignacion', cancelarAsignacion)

  }

function asingarHorario(){
   
    let ids_grupos = $("#grupo").val()
    let id_horario = $("#horario").val()
    $.ajax({
        type: "post",
        url: BASE_URL + "api/horarios.php?tipo=insertar_grupos_horario",
        data: {ids_grupos, id_horario},
        dataType: "json",
        success: function (response) {
            table.ajax.reload(null, false)
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

function cancelarAsignacion(id){
    Swal.fire({
        icon: 'question',
        title: '¿Deseas eliminar esta asignación?',
        showCancelButton:true,
        cancelButtonText: 'No',
        confirmButtonText:'Si',
        showCloseButton: true
      }).then(r=>{
        if(r.isConfirmed){

            $.ajax({
                type: "post",
                url: BASE_URL + "api/horarios.php?tipo=eliminar_grupos_horario",
                data: {id},
                dataType: "json",
                success: function (response) {
                    table.ajax.reload(null, false)
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
      })
   
}  


GeneralEventListener('bnt-asignar-horario', 'click', asingarHorario)
