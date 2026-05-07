  
  import { initCustomDataTable } from '../DataTable/datatables-init.js';
  import {GeneralEventListener, DataTableListener} from '../utils/listeners.js';

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
         
            return `
              <div class='row'>
                <div class='col-12 col-md-12'>
                  <div class="btn btn-primary btn-editar-materia">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </div>
                  <div class="btn btn-danger btn-cancelar-materia">
                    <i class="fa-solid fa-trash"></i>
                  </div>
                </div>
              </div>`;
          
        }
      }
    ];
  
    tabla = initCustomDataTable('#example', BASE_URL + 'api/materias.php?tipo=datatable', columns);
    GeneralEventListener('btn-agregar-materia', 'click', agregarMateria)
    DataTableListener(tabla, 'click', '.btn-editar-materia', editarMateria);
    DataTableListener(tabla, 'click', '.btn-cancelar-materia', cancelarMateria);

  });

  const container = document.getElementById('materias-container');
  if (container && container.dataset.autoOpen === 'true') {
      agregarMateria();
  }

  function agregarMateria(){
    Swal.fire({
      title: 'Agregar Materia', 
      html:`
       <div class="container">
          
        <div class="row mb-3">
              <div class="col-6">
                  <label for="nombre">Nombre</label>
                  <input class="form-control" id="nombre" type="text" placeholder="Ingles, Matematicas...">
              </div>
              <div class="col-6">
                  <label for="codigo">Codigo</label>
                  <input id="codigo" class="form-control" type="text" placeholder="Codigo de la materia..">
              </div>   
        </div>
       
      </div>
      `,
      didOpen:()=>{}, 
      confirmButtonText: 'Registrar',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      showCloseButton: true,
      preConfirm: (value) => {
        //Validación
        let nombre = $("#nombre").val()
  
        if(!nombre){
          Swal.showValidationMessage('Escribe un nombre para la materia')
        }
      }
  
    }).then((r)=>{
      console.log(r);
      if(r.isConfirmed){
        let nombre_materia = $("#nombre").val()
        let codigo = $("#codigo").val()
  
        $.ajax({
          type: "POST",
          url: BASE_URL +"api/materias.php?tipo=registrar",
          data: {nombre_materia, codigo},
          dataType: "JSON",
          success: function (response) {
            tabla.ajax.reload(null, false)
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
  
  function cancelarMateria(id_materia){
    Swal.fire({
      icon: 'question',
      title: '¿Deseas cancelar esta materia?',
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
      url: BASE_URL + "api/materias/2",
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
