inicializarDataTableHistorial()

function inicializarDataTableHistorial(traer_lotes = false) {
  const role = $('#role').attr("role");

  let user_project = $("#proyecto").val();
  let manzana_actual = $("#manzana").val();
  let lote_actual = $("#lote").val();


    tabla = $('#example').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        searching: true,
        destroy:true,
        pageLength: 10, // Number of rows per page
        lengthMenu: [10, 25, 50, 100],
        ajax:{
          method: 'post',
          url: '../servidor/historial/historial-consulta.php?proyecto_id=' + user_project +'&manzana='+ manzana_actual + '&lote='+ lote_actual,//
          //url: '../servidor/historial/tabla-ventas.php?proyecto=' + user_project ,//
          data: function (d) {
            d.page = Math.ceil(d.start / d.length) + 1;
        },
            dataType: 'json'
        },
        dataSrc: function (json) {
          // Obtener el número total de registros y el número de registros filtrados
          var recordsTotal = json.recordsTotal || 0;
          var recordsFiltered = json.recordsFiltered || 0;

          // Actualizar la información en el área correspondiente
          $('.dataTables_info').html('Mostrando ' + recordsFiltered + ' registros filtrados de un total de ' + recordsTotal);

          return json.data; // Devolver los datos para DataTables
      },
        responsive: true,
        lengthChange: true,
        order: [0, 'desc'],
        columns:  [
            { data:'id', title:'Folio' },
            { data:'fecha', title:'Fecha', render:(data, xd, row)=>{
                return `${row[1]} - ${row[2]}`
            } },
            { data:'cliente_etiqueta', title:'Cliente' },
            { data:'telefono', title:'Telefono' },
            { data:'direccion', title:'Dirección' },
            { data:'correo', title:'Correo' },
            { data:'usuario_nombre', title:'Usuario' },
            /* { data:'', title:'Total' }, */
            { data:'comentario', title:'Comentario'},
            { data:null, title:'Opciones', render: function(row){
              if(role == 'sales' || role == 'visitor'){
                return `
                <div class='row'>
                    <div class='col-12 col-md-12'>
                        <div class="btn btn-primary" onclick="verDetalleOrden(${row[0]})"><i class="fa-solid fa-eye"></i></div>
                    </div>
                </div>
                `
              }else{
                return `
                <div class='row'>
                    <div class='col-12 col-md-12'>
                        <div class="btn btn-primary" onclick="verDetalleOrden(${row[0]})"><i class="fa-solid fa-eye"></i></div>
                        <div class="btn btn-warning" onclick="eliminarOrden(${row[0]})"><i class="fa-solid fa-trash"></i></div>
                    </div>
                </div>
                `
              }
                
            }}
        ],
        
           // language: language_options,
    

        
        
    });

    $('#example tbody').on('contextmenu', 'tr', function () {
      var data = tabla.row($(this)).data();
      let id_venta = data[0];
      //console.log(data[3] + "'s salary is: " + data[0]);
      contextualMenu(data[0])
      $("#ver-item").attr("onclick", `verTicket(${id_venta})`)
      $("#eliminar-item").attr("onclick", `eliminarTicket(${id_venta})`)
});

if(traer_lotes){
  $.ajax({
    type: "post",
    url: "../servidor/inventario/traer-lotes-copy.php",
    data: {'proyecto':user_project,'manzana': manzana_actual},
    dataType: "json",
    success: function (response) {
      if(response.status){
        $("#lote").prop('disabled',false)
        $("#lote").empty();
        $("#lote").append('<option value="">Selecciona un lote</option>');
        for (let index = 1; index < response.cantidad_lotes; index++) {
          $("#lote").append(`<option value="${index}">${index}</option>`);
        }
      }else{
        $("#lote").prop('disabled',true);
        $("#lote").empty();
        $("#lote").append('<option value="">Selecciona un lote</option>');
      }
    }
  });
}

};


function verTicket(id){
  window.open("./vistas/nueva_orden/ticket.php?id="+ id, "_blank");

}

function resetarTabla(){
  $("#proyecto").val('');
  $("#manzana").val('');
  $("#lote").val('');
  inicializarDataTableHistorial()
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