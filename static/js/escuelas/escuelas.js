
import { initCustomDataTable } from '../DataTable/datatables-init.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
let table_escuelas;

$(document).ready(function () {
  const role = $('#role').attr("role");
  const columns = [
    { data: 'id', title: '#' },
    { data: null, title: 'Img', render: (data)=>{

      if(data.logo){
        return '<img src="'+STATIC_URL+'img/escuelas/'+data.logo+'" style="width:55px; border-radius: 8px;">'
      }else{
        return '<img src="'+STATIC_URL+'img/default.png" style="width:50px; border-radius: 8px;">'

      }
    }},
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
                <a href="${BASE_URL}escuelas/editar/${data.id}"><div class="btn btn-primary">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div></a>
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

});



