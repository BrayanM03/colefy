$(document).ready(function () {
    reloadTable()
  }); 
  

import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
let table;
let estatus_tag;

  function reloadTable(){
    const role = $('#role').attr("role");
  
    const columns = [
      { data: 'id', title: '#' },
      {
        data: 'nombre', title: 'Nombre'},
      {data: 'tipo', title: 'tipo', render: (data)=>{
        switch (data) {
          case 1:
              return 'Escolarizado'
            break;
            case 2:
              return 'Flexible'
            break;
        
          default: return 'Sin tipo'
            break;
        }
      }},
      {data: 'asignado', title: 'Asignado a'},
      { data: 'fecha_registro', title: 'Fecha reg.' },
      { data: null, title: 'Estatus' , render: (data)=>{
        if(data.estatus ==1){
          estatus_tag = '<span class="badge bg-success">Activo</span>'
        }else{
          estatus_tag = '<span class="badge bg-secondary">Inactivo</span>'
        }
        return estatus_tag;
      }},
      {
        data: null, title: 'Opciones', render: function (data, type, row) {
            return `
              <div class='row'>
                <div class='col-12 col-md-12'>
                  <div class="btn btn-primary" onclick="editarSolicitud(${row.id}, false)">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </div>
                  <div class="btn btn-warning" onclick="cancelarUsuario(${row.id})">
                    <i class="fa-solid fa-ban"></i>
                  </div>
                  <div class="btn btn-danger" onclick="mostrarHorarioPDF(${row.id})">
                    <i class="fa-solid fa-file-pdf"></i>
                  </div>
                  
                </div>
              </div>`;
          
        }
      }
    ];
  
    table = initCustomDataTable('#horarios', BASE_URL + 'api/horarios.php?tipo=horarios', columns,[[1, 'asc']]);
  };

  function cancelarHorario(id_usuario){
    Swal.fire({
      icon: 'question',
      title: '¿Deseas desactivar este horario?',
      showCancelButton:true,
      cancelButtonText: 'No',
      confirmButtonText:'Si',
      showCloseButton: true
    }).then(r=>{
      if(r.isConfirmed){
        $.ajax({
          type: "POST",
          url: BASE_URL + "servidor/historial/eliminar-registro.php",
          data: {'id_reg':id_usuario, 'tabla': 'horarios'},
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


  function mostrarHorarioPDF(id_horario){
    window.open(BASE_URL + 'horarios/pdf/'+id_horario,'_blank'); 
  }

  window.mostrarHorarioPDF = mostrarHorarioPDF