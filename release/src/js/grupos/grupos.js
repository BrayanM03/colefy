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
                <a href="editar_grupo/edit/${row.id}&nombre=${row.nombre}&nivel=${row.nivel}&grado=${row.grado}&ciclo=2025-2026"><div class="btn btn-primary">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div></a>
                <div class="btn btn-danger" onclick="cancelarGrupo(${row.id})">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
      
      }
    }
  ];

  tabla_grupo = initCustomDataTable('#grupos', BASE_URL + 'api/grupos.php?tipo=datatable', columns, [[1, 'asc']]);
};



