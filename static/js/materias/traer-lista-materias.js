  
  import { initCustomDataTable } from '../DataTable/datatables-init.js';
  let tabla;
  let estatus_tag;
  $(document).ready(function () {
    const role = $('#role').attr("role");
  
    const columns = [
      { data: 'id', title: '#' },
      {
        data: 'nombre', title: 'Nombre',
      },
       { data: 'codigo', title: 'Codigo' },
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
          if (role == 1) {
            return ''
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
  
    tabla = initCustomDataTable('#example', BASE_URL + 'api/materias.php', columns);
  });
  
  function desactivarMateria(id_materia){
    Swal.fire({
      icon: 'question',
      title: '¿Deseas desactivar esta materia?',
      showCancelButton:true,
      cancelButtonText: 'No',
      confirmButtonText:'Si',
      showCloseButton: true
    }).then(r=>{
      if(r.isConfirmed){
        $.ajax({
          type: "POST",
          url: BASE_URL + "servidor/historial/eliminar-registro.php",
          data: {'id_reg':id_materia, 'tabla': 'materias'},
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
  
  function editarMateria(id_materia){
    $.ajax({
      type: "post",
      url: BASE_URL + "servidor/materias/obtener-info-materia.php",
      data: {id_materia},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          Swal.fire({
            html: `
                <div class="container">
                   <div class="row mb-3">
                      <div class="col-md-6 col-12">
                          <label>Nombre</label>
                          <input class="form-control" type="text" id="materia" value="${response.datos.nombre}" placeholder="Materia"/>
                      </div>
                      <div class="col-md-6 col-12">
                          <label>Codigo</label>
                          <input class="form-control" type="text" id="codigo" value="${response.datos.codigo}" placeholder="Codigo"/>
                      </div>
                   </div>
                   <div class="row mb-3">
                      <div class="col-md-6 col-12">
                          <label>Valor creditos</label>
                          <input class="form-control" type="number" value="${response.datos.valor_creditos}" id="valor_credito" placeholder="0"/>
                      </div>
                   </div>
                </div>
            `,
            showCloseButton: true,
            confirmButtonText: 'Actualizar'
          }).then(r =>{
            if(r.isConfirmed){
              let nombre = document.getElementById('materia').value
              let codigo = document.getElementById('codigo').value
              let valor_credito = document.getElementById('valor_credito').value
             
              $.ajax({
                type: "post",
                url: BASE_URL + "servidor/materias/actualizar.php",
                data: {id_materia, nombre, codigo, valor_credito},
                dataType: "json",
                success: function (response) {
                  if(response.estatus){
                      Swal.fire({
                        icon: 'success',
                        title: response.mensaje
                      })
                  }else{
                    Swal.fire({
                      icon: 'error',
                      title: response.mensaje
                    })
                  }
                  tabla.ajax.reload(null, false)
                }
              });
            }
          })
        }
      }
    });
    
  }
