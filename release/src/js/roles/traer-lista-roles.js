import { initCustomDataTable } from '../DataTable/datatables-init.js';
let table;
$(document).ready(function () {
  const role = $('#role').attr("role");
  let estatus_tag;
  const columns = [
    { data: 'id', title: '#' },
    {data: 'nombre', title: 'Nombre'},
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
              <a href="${BASE_URL}roles/editar/${row.id}#permisos">
                <div class="btn btn-primary">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div>
                </a>
                <div class="btn btn-danger" onclick="cancelarUsuario(${row.id})">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
       
      }
    }
  ];

  table = initCustomDataTable('#roles', BASE_URL+ '/api/roles.php?tipo=datatable', columns);
});


function editarRol(id_rol){
    console.log(id_rol);
}
