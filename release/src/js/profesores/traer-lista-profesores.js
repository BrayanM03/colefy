import { initCustomDataTable } from '../DataTable/datatables-init.js';
let table;
$(document).ready(function () {
  const role = $('#role').attr("role");
  let estatus_tag;
  const columns = [
    { data: 'id', title: '#' },
    {
      data: null, title: 'Nombre', render: function (data, type, row) {
        return row['nombre'] + ' ' + row['apellido'];
      }
    },
     { data: 'telefono', title: 'Telefono' },
     { data: 'correo', title: 'Correo' },
     { data: null, title: 'Estatus' , render: (data)=>{
      if(data.estatus ==1){
        estatus_tag = '<span class="badge bg-success">Activo</span>'
      }else{
        estatus_tag = '<span class="badge bg-secondary">Inactivo</span>'
      }
      return estatus_tag;
    }},
   /* {
      data: null, title: 'Ciudad', render: function (data, type, row) {
        return row['ciudad'] + ', ' + row['estado'];
      }
    }, */
   /*  { data: 'telefono', title: 'Teléfono' },
    { data: 'correo', title: 'Correo' }, */
    {
      data: null, title: 'Opciones', render: function (data, type, row) {
        if (role == 1) {
          return `
            <div class='row'>
              <div class='col-12 col-md-12'>
                <div class="btn btn-primary" onclick="editarSolicitud(${row.id}, false)">
                  <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div class="btn btn-danger" onclick="cancelarUsuario(${row.id})">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
        } else {
          return '';
        }
      }
    }
  ];

  table = initCustomDataTable('#example', BASE_URL + 'api/profesores.php', columns);
});


function cancelarUsuario(id_usuario){
  Swal.fire({
    icon: 'question',
    title: '¿Deseas desactivar este usuario?',
    showCancelButton:true,
    cancelButtonText: 'No',
    confirmButtonText:'Si',
    showCloseButton: true
  }).then(r=>{
    if(r.isConfirmed){
      $.ajax({
        type: "POST",
        url: BASE_URL + "servidor/historial/eliminar-registro.php",
        data: {'id_reg':id_usuario, 'tabla': 'usuarios'},
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


