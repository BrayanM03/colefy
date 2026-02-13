
$(document).ready(function () {
    const role = $('#role').attr("role");
    tabla = $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax:{
            url: '../servidor/documentos/server_processing.php',//
            
            dataType: 'json'
        },
        responsive: true,
        order: [0, 'desc'],
        columns:  [
            { data:0, title:'#' },
            { data:1, title:'Titulo' },
            { data:2, title:'Descripción' },
            { data:3, title:'Fecha' },
            { data:4, title:'Hora' },
            { data:5, title:'Mestro' },
            /* { data:5, title:'Profesor' }, */
            { data:null, title:'Opciones', render: function(row){

              if(role != 1 || role != 2){
                return `
                <div class='row'>
                    <div class='col-12 col-md-12'>
                        <div class="btn btn-info" onclick="verDocumentos(${row[0]})">
                            <i class="fa-solid fa-eye"></i>
                        </div> 
                        <div class="btn btn-danger" onclick="eliminarRegistro(${row[0]}, 1, 'Registro')">
                            <i class="fa-solid fa-trash"></i>
                        </div>
                    </div>
                </div>
                `
              }else{
                return `
                <div class='row'>
                     <div class='col-12 col-md-12'>
                      <div class="btn btn-info" onclick="verDocumentos(${row[0]})">
                          <i class="fa-solid fa-eye"></i>
                      </div>
                   </div>
                </div>
                `
              }
                
            }}
        ],
        
            language: language_options,
        
    });
});


function verDocumentos(id_documento){
  console.log(id_documento);
  $.ajax({
    type: "post",
    url: "./../servidor/documentos/traer-registro.php",
    data: {id_documento},
    dataType: "json",
    success: function (response) {
        if(response.estatus){
          Swal.fire({
            width: '900px',
            html:`
            <h3 class="mb-3"><b>Documentos cargados</b><h3>
              <div class="row mb-3">
                  <div class="col-md-12 col-12">
                      <h3 class><b>${response.datos.titulo}</b></h3>
                      <span>${response.datos.descripcion}</span>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12 col-12">
                    <ul class="list-group">
                      <li class="list-group-item list-group-item-action">
                          <div class="row">
                              <div class="col-md-1 col-12">
                                  #
                              </div>
                              <div class="col-md-6 col-12">
                                  Nombre
                              </div>
                              <div class="col-md-3 col-12">
                                  Preview
                              </div>
                              <div class="col-md-2 col-12">
                              Descargar
                          </div>
                          </div>
                      </li>
                      <div id="lista-documentos"></div>
                    </ul>
                  </div>
              </div>
            `,
            didOpen: function(){
              let lista_documentos_area = $('#lista-documentos');
              if(response.estatus){
                let count= 0;
                response.datos.archivos.forEach(element => {
                  count++;
                  lista_documentos_area.append(`
                  <li class="list-group-item list-group-item-action">
                      <div class="row">
                          <div class="col-md-1 col-12">
                              ${count}
                          </div>
                          <div class="col-md-6 col-12">
                              ${element.nombre_documento}
                          </div>
                          <div class="col-md-3 col-12">
                           <div id="area-canvas_${count}"></div>
                          </div>
                          <div class="col-md-2 col-12">
                               <div id="descargar" onclick="descargarArchivo('./docs/${id_documento}/${element.ruta}', '${element.nombre_documento}')" class="btn btn-success"><i class="fa-solid fa-download"></i></div>
                               <div class="btn btn-danger" onclick="eliminarRegistro(${element.id},2,'Documento')"><i class="fa-solid fa-trash"></i></div>                 
                          </div>
                      </div>
                  </li>
                  `)
                 cargarDocumentoMiniatura(`area-canvas_${count}`, id_documento, element.ruta, element.id, element.extension)
                });
               
               }else{
                lista_documentos_area.append(`
                <li class="list-group-item list-group-item-action">
                <div class="row">
                    <div class="col-md-12 text-center col-12">
                      Hubo un error y no se cargaron los documentos
                    </div>
                    </div>
                </li>
                `);
               }
            }
          })
        }
    }
  });
}


function cargarDocumentoMiniatura(area_canvas_id,id_documento, ruta, id_detalle, extension_archivo){
  let area_canvas = $(`#${area_canvas_id}`);
  $.ajax({
    url: "./docs/"+id_documento+"/"+ruta,
    method: "GET",
    xhr: function() {
      var xhr = new window.XMLHttpRequest();
      xhr.responseType = "arraybuffer";
      return xhr;
    },
    success: function(data) {
     if(extension_archivo =='pdf'){
        var pdfData = new Uint8Array(data);
        var pdfURL = URL.createObjectURL(new Blob([pdfData], { type: "application/pdf" }));
    
        // Continuar con el código original usando `pdfURL`
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
          // Obtiene el elemento canvas
          console.log(area_canvas);
          area_canvas.empty().append(`
          
          <canvas id="thumbnailCanvas_${id_detalle}" style="width:70px !important;"></canvas>`)
          var canvas = document.getElementById('thumbnailCanvas_'+id_detalle);

          // Carga el PDF
          pdfjsLib.getDocument(pdfURL).promise.then(function(pdfDoc) {
          // Obtiene la primera página del PDF (página 1)
          var pageNumber = 1;
            console.log(id_detalle);
          // Renderiza la página en el canvas
          pdfDoc.getPage(pageNumber).then(function(page) {
              var viewport = page.getViewport({ scale: 0.3 });
              var context = canvas.getContext('2d');
              canvas.width = viewport.width;
              canvas.height = viewport.height;

              var renderContext = {
              canvasContext: context,
              viewport: viewport,
              };
              page.render(renderContext).promise.then(function() {

              });
          });
          });
     }else if(extension_archivo === 'jpg' || extension_archivo === 'png' || extension_archivo === 'jpeg') {
        let random_v = Math.floor(Math.random() * 900000) + 100000;
        area_canvas.empty().append(`
          <img style="width:70px !important;" src="./docs/${id_documento}/${ruta}?v=${random_v}">`)
     }else{
      let random_v = Math.floor(Math.random() * 900000) + 100000;
      area_canvas.empty().append(`
      <img style="width:70px !important;" src="./img/documento.jpg?v=${random_v}">`)
     }

     $(`#${area_canvas_id}`).attr('documento', true)
      
    },
    error: function(message){
        area_canvas.empty().append(`
        <div class="contenedor-cargar-documento" id="contenedor-cargar-documento">Sin factura cargada</div>
        
        `)
        $("#area-canvas").attr('documento', false)
    }

  });
}

// Función para descargar el archivo
function descargarArchivo(url, nombreArchivo) {
  fetch(url)
      .then(response => response.blob()) // Convertir la respuesta en un Blob
      .then(blob => {
          // Crear una URL para el Blob
          const urlBlob = window.URL.createObjectURL(blob);

          // Crear un enlace de descarga
          const enlace = document.createElement('a');
          enlace.href = urlBlob;
          enlace.download = nombreArchivo; // Nombre del archivo que se descargará
          document.body.appendChild(enlace);

          // Hacer clic en el enlace para iniciar la descarga
          enlace.click();

          // Eliminar el enlace una vez descargado
          document.body.removeChild(enlace);
      })
      .catch(error => console.error('Error al descargar el archivo:', error));
}

let language_options = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "infoThousands": ",",
    "loadingRecords": "Cargando...",
    "paginate": {
      "first": "Primero",
      "last": "Último",
      "next": "Siguiente",
      "previous": "Anterior"
    },
    "aria": {
      "sortAscending": ": Activar para ordenar la columna de manera ascendente",
      "sortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
      "copy": "Copiar",
      "colvis": "Visibilidad",
      "collection": "Colección",
      "colvisRestore": "Restaurar visibilidad",
      "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.",
      "copySuccess": {
        "1": "Copiada 1 fila al portapapeles",
        "_": "Copiadas %ds fila al portapapeles"
      },
      "copyTitle": "Copiar al portapapeles",
      "csv": "CSV",
      "excel": "Excel",
      "pageLength": {
        "-1": "Mostrar todas las filas",
        "_": "Mostrar %d filas"
      },
      "pdf": "PDF",
      "print": "Imprimir",
      "renameState": "Cambiar nombre",
      "updateState": "Actualizar",
      "createState": "Crear Estado",
      "removeAllStates": "Remover Estados",
      "removeState": "Remover",
      "savedStates": "Estados Guardados",
      "stateRestore": "Estado %d"
    },
    "autoFill": {
      "cancel": "Cancelar",
      "fill": "Rellene todas las celdas con <i>%d</i>",
      "fillHorizontal": "Rellenar celdas horizontalmente",
      "fillVertical": "Rellenar celdas verticalmentemente"
    },
    "decimal": ",",
    "searchBuilder": {
      "add": "Añadir condición",
      "button": {
        "0": "Constructor de búsqueda",
        "_": "Constructor de búsqueda (%d)"
      },
      "clearAll": "Borrar todo",
      "condition": "Condición",
      "conditions": {
        "date": {
          "after": "Despues",
          "before": "Antes",
          "between": "Entre",
          "empty": "Vacío",
          "equals": "Igual a",
          "notBetween": "No entre",
          "notEmpty": "No Vacio",
          "not": "Diferente de"
        },
        "number": {
          "between": "Entre",
          "empty": "Vacio",
          "equals": "Igual a",
          "gt": "Mayor a",
          "gte": "Mayor o igual a",
          "lt": "Menor que",
          "lte": "Menor o igual que",
          "notBetween": "No entre",
          "notEmpty": "No vacío",
          "not": "Diferente de"
        },
        "string": {
          "contains": "Contiene",
          "empty": "Vacío",
          "endsWith": "Termina en",
          "equals": "Igual a",
          "notEmpty": "No Vacio",
          "startsWith": "Empieza con",
          "not": "Diferente de",
          "notContains": "No Contiene",
          "notStarts": "No empieza con",
          "notEnds": "No termina con"
        },
        "array": {
          "not": "Diferente de",
          "equals": "Igual",
          "empty": "Vacío",
          "contains": "Contiene",
          "notEmpty": "No Vacío",
          "without": "Sin"
        }
      },
      "data": "Data",
      "deleteTitle": "Eliminar regla de filtrado",
      "leftTitle": "Criterios anulados",
      "logicAnd": "Y",
      "logicOr": "O",
      "rightTitle": "Criterios de sangría",
      "title": {
        "0": "Constructor de búsqueda",
        "_": "Constructor de búsqueda (%d)"
      },
      "value": "Valor"
    },
    "searchPanes": {
      "clearMessage": "Borrar todo",
      "collapse": {
        "0": "Paneles de búsqueda",
        "_": "Paneles de búsqueda (%d)"
      },
      "count": "{total}",
      "countFiltered": "{shown} ({total})",
      "emptyPanes": "Sin paneles de búsqueda",
      "loadMessage": "Cargando paneles de búsqueda",
      "title": "Filtros Activos - %d",
      "showMessage": "Mostrar Todo",
      "collapseMessage": "Colapsar Todo"
    },
    "select": {
      "cells": {
        "1": "1 celda seleccionada",
        "_": "%d celdas seleccionadas"
      },
      "columns": {
        "1": "1 columna seleccionada",
        "_": "%d columnas seleccionadas"
      },
      "rows": {
        "1": "1 fila seleccionada",
        "_": "%d filas seleccionadas"
      }
    },
    "thousands": ".",
    "datetime": {
      "previous": "Anterior",
      "next": "Proximo",
      "hours": "Horas",
      "minutes": "Minutos",
      "seconds": "Segundos",
      "unknown": "-",
      "amPm": [
        "AM",
        "PM"
      ],
      "months": {
        "0": "Enero",
        "1": "Febrero",
        "2": "Marzo",
        "3": "Abril",
        "4": "Mayo",
        "5": "Junio",
        "6": "Julio",
        "7": "Agosto",
        "8": "Septiembre",
        "9": "Octubre",
        "10": "Noviembre",
        "11": "Diciembre"
      },
      "weekdays": [
        "Dom",
        "Lun",
        "Mar",
        "Mie",
        "Jue",
        "Vie",
        "Sab"
      ]
    },
    "editor": {
      "close": "Cerrar",
      "create": {
        "button": "Nuevo",
        "title": "Crear Nuevo Registro",
        "submit": "Crear"
      },
      "edit": {
        "button": "Editar",
        "title": "Editar Registro",
        "submit": "Actualizar"
      },
      "remove": {
        "button": "Eliminar",
        "title": "Eliminar Registro",
        "submit": "Eliminar",
        "confirm": {
          "1": "¿Está seguro que desea eliminar 1 fila?",
          "_": "¿Está seguro que desea eliminar %d filas?"
        }
      },
      "error": {
        "system": "Ha ocurrido un error en el sistema (<a target=\"\\\"rel=\"\\nofollow\"href=\"\\\">Más información&lt;\\/a&gt;).</a>"
      },
      "multi": {
        "title": "Múltiples Valores",
        "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
        "restore": "Deshacer Cambios",
        "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
      }
    },
    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
    "stateRestore": {
      "creationModal": {
        "button": "Crear",
        "name": "Nombre:",
        "order": "Clasificación",
        "paging": "Paginación",
        "search": "Busqueda",
        "select": "Seleccionar",
        "columns": {
          "search": "Búsqueda de Columna",
          "visible": "Visibilidad de Columna"
        },
        "title": "Crear Nuevo Estado",
        "toggleLabel": "Incluir:"
      },
      "emptyError": "El nombre no puede estar vacio",
      "removeConfirm": "¿Seguro que quiere eliminar este %s?",
      "removeError": "Error al eliminar el registro",
      "removeJoiner": "y",
      "removeSubmit": "Eliminar",
      "renameButton": "Cambiar Nombre",
      "renameLabel": "Nuevo nombre para %s",
      "duplicateError": "Ya existe un Estado con este nombre.",
      "emptyStates": "No hay Estados guardados",
      "removeTitle": "Remover Estado",
      "renameTitle": "Cambiar Nombre Estado"
    }
  }