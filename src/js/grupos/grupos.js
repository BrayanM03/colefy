$(document).ready(function () {
  reloadTable()
});
import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
let tabla_grupo;
let estatus_tag;

function reloadTable(){
  const role = $('#role').attr("role");
 
  const columns = [
    { data: 'id', title: '#' },
    {data: 'nombre', title: 'Nombre'},
    {data: 'nivel', title: 'Nivel'},
    {data: 'grado', title: 'Grado'},
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
                <a href="${BASE_URL}grupos/edit/${row.id}"><div class="btn btn-primary">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div></a>
                <div class="btn btn-danger btn-cancelar-grupo">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
      
      }
    }
  ];

  tabla_grupo = initCustomDataTable('#grupos', BASE_URL + 'api/grupos.php?tipo=datatable', columns, [[1, 'asc']]);
  DataTableListener(tabla_grupo, 'click', '.btn-cancelar-grupo', cancelarGrupo);

};


function cancelarGrupo(id_grupo){
  Swal.fire({
    icon: 'question',
    title: '¿Deseas cancelar este grupo?',
    html: `Los horarios y los alumnos ligados a este grupo quedaran disponibles`,
    showCancelButton:true,
    cancelButtonText: 'No',
    confirmButtonText:'Si',
    showCloseButton: true
  }).then(r=>{
    if(r.isConfirmed){
      $.ajax({
        type: "POST",
        url: BASE_URL + "api/grupos.php?tipo=cancelar",
        data: {id_grupo},
        dataType: "JSON",
        success: function (response) {
            if(response.estatus){
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
                        tabla_grupo.ajax.reload(false)

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
                        tabla_grupo.ajax.reload(false)


                    }
                })
            }

            
        }
    });
    }
  })
}
