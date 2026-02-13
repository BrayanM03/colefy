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
  
  let ciclo_escolar = $("#ciclo").val()
  let id_grupo =getParameterByName('id_grupo')
  let nivel =getParameterByName('nivel')
  let grado =getParameterByName('grado')
  let nombre_grupo =getParameterByName('nombre')
  import { initCustomDataTable } from '../DataTable/datatables-init.js';
  import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
  let table;
  let estatus_tag;

  $("#nombre-grupo").val(nombre_grupo)
  $("#nivel").val(nivel)
  $("#grado").val(grado)

  reloadTable()
  function reloadTable(){
    const role = $('#role').attr("role");
  
    const columns = [
      { data: 'id', title: '#' },
      {data: 'nombre', title: 'Nombre'},
      {data: null, title: 'Apellidos', render:(data)=>{
          return data.apellido_paterno + ' ' + data.apellido_materno
      }},
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
          if(data.estatus != 1){
            return `
            <div class='row'>
            <div class='col-12 col-md-12'>
              <div class="btn btn-info btn-reactivar">
              <i class="fa-solid fa-rotate-left"></i>
              </div>
            </div>
          </div>
            `
          }
          return `
          <div class='row'>
            <div class='col-12 col-md-12'>
              <div class="btn btn-danger btn-cancelar">
                <i class="fa-solid fa-trash"></i>
              </div>
            </div>
          </div>`;
        }
      }
    ];
  
   
    table = initCustomDataTable('#detalle_grupo', BASE_URL + 'api/grupos.php?tipo=datatable_grupo&id_grupo='+id_grupo+'&ciclo='+ciclo_escolar, columns)
    DataTableListener(table, 'click', '.btn-cancelar', cancelarAlumnoGrupo)
    DataTableListener(table, 'click', '.btn-reactivar', reactivarAlumnoGrupo)

  }
  
  function actualizarGrupo(){
      let nombre = $("#nombre-grupo").val()
      console.log(nombre);
      let grado = $("#grado").val()
      let nivel = $("#nivel").val()
      let ciclo = $("#ciclo").val()
  
      if(!nombre){
        Toast.fire({
          icon: "error",
          title: 'Escribe un nombre para el grupo'
        });
      }
      if(!grado){
        Toast.fire({
          icon: "error",
          title: 'Seleccione un grado'
        });
      }
      if(!nivel){
        Toast.fire({
          icon: "error",
          title: 'Selecciona un nivel'
        });
      }
  
      $.ajax({
        type: "post",
        url: BASE_URL + "api/grupos.php?tipo=actualizar",
        data: {nombre, nivel, grado,ciclo, id_grupo},
        dataType: "json",
        success: function (response) {
          table.ajax.reload(null, false)
          if(response.estatus){
            Swal.fire({
              icon: "success",
              title: response.mensaje,
              confirmButtonText: 'Entendido',
              showCloseButton: true,
              html: `<h4 style="color:rgba(100, 150, 200)">${response.subtitulo}</h4>`
  
            });
            $("#nombre-horario").val('')
            $("#grado").val('')
            $("#nivel").val('')
            $("#ciclo").val('')
  
          }else{
            Swal.fire({
              icon: "error",
              title: response.mensaje,
              confirmButtonText: 'Entendido',
              showCloseButton: true,
              html: `<h4 style="color:rgba(100, 150, 200)">${response.subtitulo}</h4>`
            });
          }
        }
      });
    }
  
  
    let debounceTimer;
  
    $('#alumno').on('shown.bs.select', function () {
      let $input = $('.bs-searchbox input'); // Input de búsqueda interno
    
      $input.off('keyup.miEvento').on('keyup.miEvento', function () {
        clearTimeout(debounceTimer); // Cancelar timeout anterior
    
  
        const value = $(this).val().trim();
      if (!value) return; // si no hay texto, no hacemos nada
  
        debounceTimer = setTimeout(() => {
          console.log('Buscando:', value);
          // Aquí llamas tu función para hacer la petición AJAX
          buscarAlumno(value);
        }, 600); // Espera 600ms desde la última tecla
      });
    });
  
    function buscarAlumno(busqueda){
      $.ajax({
        type: "post",
        url: BASE_URL + "api/alumnos.php?tipo=combo",
        data: {busqueda},
        dataType: "json",
        success: function (response) {
          if(response.estatus){
            console.log(response);
            $("#alumno").empty()
            response.data.forEach(element => {
              $("#alumno").append(`<option value="${element.id}">${element.nombre +' '+ element.apellido_paterno+' '+element.apellido_materno }</option>`)
            });
            $('#alumno').selectpicker('refresh');
  
          }
        }
      });
    }
  
    function registrarAlumnoGrupo(){
      let id_alumno = $("#alumno").val()
      let id_ciclo = $("#ciclo").val()

      $.ajax({
        type: "post",
        url: BASE_URL + "api/grupos.php?tipo=registrar_alumno",
        data: {id_alumno, id_grupo, id_ciclo},
        dataType: "json",
        success: function (response) {
          table.ajax.reload(null, false)
          if(response.estatus){
            Toast.fire({
              icon: "success",
              title: response.mensaje
            });
          }else{
            Toast.fire({
              icon: "error",
              title: response.mensaje
            });
          }
        }
      });
    }
  
    function cancelarAlumnoGrupo(id_registro){
        Swal.fire({
            icon: 'question',
            title: '¿Deseas dar de baja este alumno del grupo?',
            showCancelButton:true,
            cancelButtonText: 'No',
            confirmButtonText:'Si',
            showCloseButton: true
          }).then(r=>{
            if(r.isConfirmed){
                $.ajax({
                    type: "post",
                    url: BASE_URL + "api/grupos.php?tipo=cancelar_alumno_grupo",
                    data: {id_registro},
                    dataType: "json",
                    success: function (response) {
                      table.ajax.reload(null, false)
                      if(response.estatus){
                        Toast.fire({
                          icon: "success",
                          title: response.mensaje,
                        
                        });
                      }else{
                        Toast.fire({
                          icon: "error",
                          title: response.mensaje,
                        
                        });
                      }
                    }
                  });
            }})
      
    }

    function reactivarAlumnoGrupo(id_registro){
        Swal.fire({
            icon: 'question',
            title: '¿Deseas reactivar este alumno del grupo?',
            showCancelButton:true,
            cancelButtonText: 'No',
            confirmButtonText:'Si',
            showCloseButton: true
          }).then(r=>{
            if(r.isConfirmed){
                $.ajax({
                    type: "post",
                    url: BASE_URL + "api/grupos.php?tipo=reactivar_alumno_grupo",
                    data: {id_registro},
                    dataType: "json",
                    success: function (response) {
                      table.ajax.reload(null, false)
                      if(response.estatus){
                        Toast.fire({
                          icon: "success",
                          title: response.mensaje,
                        
                        });
                      }else{
                        Toast.fire({
                          icon: "error",
                          title: response.mensaje,
                        
                        });
                      }
                    }
                  });
            }})
      
    }

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    //Listeners
    GeneralEventListener('btn-actualizar-grupo', 'click', actualizarGrupo)
    GeneralEventListener('btn-registrar-alumno-grupo', 'click', registrarAlumnoGrupo);
