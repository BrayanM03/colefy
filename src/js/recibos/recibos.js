

import { initCustomDataTable } from '../DataTable/datatables-init.js'; 
import {GeneralEventListener} from '../utils/listeners.js';

let debounceTimer;
let table
$(document).ready(function () {
    const columns = [
      { data: null, title: '#' },
      { data: null, title: 'Folio', render: (data)=>{
        return 'REC-' + data.folio
      } },
       { data: 'alumno', title: 'Alumno' },
       { data: 'monto_total', title: 'Monto' },
       { data: null, title: 'Pagado', render: function(data){
        return parseFloat(data.monto_total) - parseFloat(data.saldo_pendiente) 
       }},
       { data: 'saldo_pendiente', title: 'Saldo' },
       { data: null, title: 'tipo', render: function(data){
          if(data.tipo ==1){
            return 'Contado'
          }else if(data.tipo ==2){
            return 'Parcial'
          }else{
            return 'Indefinido'
          }
       } },
       { data: 'ciclo', title: 'Ciclo escolar' },
       { data: 'fecha_registro', title: 'Fecha - hora' },
       { data: null, title: 'Estatus' , render: (data)=>{
          let estatus_tag='';
       
         if(data.estatus ==1){
           estatus_tag = '<span class="badge bg-info">Emitido</span>'
          }else if(data.estatus ==0){
            estatus_tag = '<span class="badge bg-secondary">Cancelado</span>'
          }else if(data.estatus ==2){
            estatus_tag = '<span class="badge" style="background-color:orange;">Abonado</span>'
          }else if(data.estatus ==3){
            estatus_tag = '<span class="badge bg-success">Pagado</span>'
          }else if(data.estatus ==4){
            estatus_tag = '<span class="badge bg-danger">Vencido</span>'
          }else if(data.estatus ==5){
            estatus_tag = '<span class="badge bg-warning">Condonado</span>'
          }else if(data.estatus ==6){ 
            estatus_tag = '<span class="badge bg-dark">Pendiente</span>'
          }else if(data.estatus ==7){
            estatus_tag = '<span class="badge bg-light">Verificando</span>'
          }
          return estatus_tag;
        }},
        { data: 'comentario', title: 'Comentario' },
      {
        data: null, title: 'Opciones', render: function (data) {
        
            let tipo_cancel = data.estatus ? 1 : 2
            let btn_fa =  data.estatus ? 'fa-ban' : 'fa-rotate-left'
            let btn_color = data.estatus ? 'btn-warning' : 'btn-info'
            let alt_text = data.estatus ? 'Cancelar recibo' : 'Descancelar recibo'
            return `
              <div class='row'>
                <div class='col-12 col-md-12'>
                  <div class="btn" style="background-color:tomato; color:white;" onclick="mostrarRecibo(${data.id})">
                    <i class="fa-solid fa-file-pdf"></i>
                  </div>
                  <div class="btn btn-primary" onclick="editarRecibo(${data.id},${data.folio}, false)">
                     <i class="fa-solid fa-pen-to-square"></i>
                  </div>
                  <div class="btn ${btn_color}" onclick="cancelarRecibo(${data.id}, ${tipo_cancel})" alt="${alt_text}">
                    <i class="fa-solid ${btn_fa}"></i>
                  </div> 
                </div>
              </div>`;
          
        }
      }
    ];
  
    const ajaxConfig = {
      url: BASE_URL + 'api/recibos.php?tipo=datatable',
      type: 'POST', // Recomendado para filtros
      data: function (d) {
          // Capturamos los valores de los inputs de tu HTML
          d.f_alumno = $('#f-alumno').val();
          d.f_tipo = $('#f-tipo').val();
          d.f_inicio = $('#f-fecha-inicio').val();
          d.f_fin = $('#f-fecha-fin').val();
          d.f_estatus = $('#f-estatus').val();
      }
  };

    table = initCustomDataTable('#recibos', ajaxConfig, columns);
    GeneralEventListener('btn-buscar', 'click', reloadTable)

    function reloadTable() { 
      table.ajax.reload(null, false);
     }
    
  });

  function mostrarRecibo(id_recibo){
    window.open('./recibos/pdf/'+id_recibo,'_blank');
  }

  function cancelarRecibo(id_recibo, tipo_cancelacion){
    let preg;
    if(tipo_cancelacion==1){
      preg =  '¿Deseas cancelar este recibo?'
    }else{
      preg =  '¿Deseas descancelar este recibo?'
    }

    Swal.fire({
      icon: 'question',
      title: preg,
      showCancelButton:true,
      cancelButtonText: 'No',
      confirmButtonText:'Si',
      showCloseButton: true
    }).then(r=>{
      if(r.isConfirmed){
        $.ajax({
          type: "POST",
          url:  BASE_URL + "api/recibos.php?tipo=actualizar_estatus_recibo",
          data: {'id_recibo':id_recibo, 'tipo_cancelacion': tipo_cancelacion},
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
                          table.ajax.reload(false)
  
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
                          table.ajax.reload(false)
  
  
                      }
                  })
              }
         
          }
      });
      }
    })
  }

  function editarRecibo(id, folio){
    window.open(BASE_URL + 'recibos/editar/' + id, '_blank');

  /*   const form = document.createElement('form');
    form.method = 'POST'; // Importante: Método POST
    form.action = BASE_URL + 'recibos/editar/' + folio;
    form.target = '_blank'; // Para abrir en una nueva pestaña

    // 2. Crear campos ocultos para el ID y el FOLIO
    const idField = document.createElement('input');
    idField.type = 'hidden';
    idField.name = 'id'; // El nombre que usarás en PHP: $_POST['id']
    idField.value = id;

    const folioField = document.createElement('input');
    folioField.type = 'hidden';
    folioField.name = 'folio_interno'; // El nombre que usarás en PHP: $_POST['folio_interno']
    folioField.value = folio;

    // 3. Agregar los campos al formulario
    form.appendChild(idField);
    form.appendChild(folioField);

    // 4. Agregar el formulario al cuerpo del documento (es necesario para enviarlo)
    document.body.appendChild(form);

    // 5. Enviar el formulario (esto abre la nueva pestaña)
    form.submit();

    // 6. Remover el formulario (limpieza)
    document.body.removeChild(form); */

    //window.open('../static/editar-recibo.php?id=' + id + '&folio_interno='+folio, '_blank')
  }

  $('#f-alumno').on('shown.bs.select', function () {
    let $input = $('.bs-searchbox input');

    $input.off('keyup.miEvento').on('keyup.miEvento', function (e) {

        // Teclas que NO deben disparar búsqueda
        const teclasIgnoradas = [
            'ArrowUp',
            'ArrowDown',
            'ArrowLeft',
            'ArrowRight',
            'Enter',
            'Tab',
            'Escape'
        ];

        if (teclasIgnoradas.includes(e.key)) return;

        clearTimeout(debounceTimer);

        const value = $(this).val().trim();
        if (!value) return;

        debounceTimer = setTimeout(() => {
            buscarAlumno(value);
        }, 400);
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
          $("#f-alumno").empty()
          response.data.forEach(element => {
            $("#f-alumno").append(`<option value="${element.id}">${element.nombre +' '+ element.apellido_paterno+' '+element.apellido_materno }</option>`)
          });
          $('#f-alumno').selectpicker('refresh');
  
        }
      }
    });
  }

  //Exposición global
  window.mostrarRecibo = mostrarRecibo; 
  window.cancelarRecibo = cancelarRecibo; 
  window.editarRecibo = editarRecibo; 
 


  
  
  