  
  import { initCustomDataTable } from '../DataTable/datatables-init.js';
  import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
 
  let tabla;
  let estatus_tag;
  $(document).ready(function () {
    const role = $('#role').attr("role");
    const options =   [{ width: '10%' }, null, null, null, null]
    const roles = {1: 'Admin', 2: 'Profesor', 3: 'Normal', 4: 'Contraloria'};
    const columns = [
      { data: 'id', title: '#' },
      { data: 'id', title: 'Foto', render:()=>{
        return `<img class="rounded" style="width:40px" src="static/img/icons/not-user-img.jpg">`
      }},
      {data: null, title: 'Nombre', render: (data)=>{
        return data['nombre'] + ' ' + data['apellido']
      }},
      { data: null, title: 'Rol', render: (data)=>{
        return roles[data.rol]
      }},
      { data: 'usuario', title: 'Usuario' },
      { data: null, title: 'Contraseña', render: (data)=>{
          let pass= data.contraseña.substring(0, 10);
          const pass_ft = pass + '...'
          return pass_ft
      }},
      { data: null, title: 'Estatus' , render: (data)=>{
        if(data.estatus ==1 || data.estatus == 2){
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
                  <a href="${BASE_URL}usuarios/editar/${row.id}#permisos">
                  <div class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </div>
                </a>
                </div>
              </div>`;
         
        }
      }
    ];
  
    tabla = initCustomDataTable('#usuarios', './api/usuarios.php?tipo=datatable', columns, [[2, 'asc']], options);
  });
 