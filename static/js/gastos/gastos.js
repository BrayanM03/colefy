

import { initCustomDataTable } from '../DataTable/datatables-init.js'; 
import {GeneralEventListener} from '../utils/listeners.js';

let debounceTimer;
let table
$(document).ready(function () {
    const columns = [
        { 
            data: null, 
            title: '#',
            render: function(data, type, row, meta){
                return meta.row + 1;
            }
        },
        { 
            data: 'id', 
            title: 'Folio',
            render: (data) => 'GST-' + String(data).padStart(5, '0')
        },
        { 
            data: 'fecha', 
            title: 'Fecha'
        },
        { 
            data: 'ciclo', 
            title: 'Ciclo escolar'
        },
        { 
            data: 'categoria', 
            title: 'Categoría'
        },
        { 
            data: 'subcategoria', 
            title: 'Subcategoría'
        },
        { 
            data: 'concepto', 
            title: 'Concepto'
        },
        { 
            data: 'proveedor', 
            title: 'Proveedor',
            render: (data) => data ?? '<span class="text-muted">—</span>'
        },
        { 
            data: null, 
            title: 'Tipo comprobante',
            render: function(data){
                const tipos = {
                    'factura_cfdi'      : '<span class="badge bg-success">Factura CFDI</span>',
                    'nota_remision'     : '<span class="badge bg-primary">Nota de remisión</span>',
                    'ticket_recibo'     : '<span class="badge bg-secondary">Ticket / Recibo</span>',
                    'sin_comprobante'   : '<span class="badge bg-warning text-dark">Sin comprobante</span>'
                };
                return tipos[data.tipo_comprobante] ?? '<span class="badge bg-light text-dark">Desconocido</span>';
            }
        },
        { 
            data: null, 
            title: 'Forma de pago',
            render: function(data){
                const formas = {
                    'efectivo'          : '<i class="fa-solid fa-money-bill-wave text-success"></i> Efectivo',
                    'transferencia'     : '<i class="fa-solid fa-building-columns text-primary"></i> Transferencia',
                    'tarjeta_debito'    : '<i class="fa-solid fa-credit-card text-info"></i> Débito',
                    'tarjeta_credito'   : '<i class="fa-solid fa-credit-card text-warning"></i> Crédito',
                    'cheque'            : '<i class="fa-solid fa-money-check text-secondary"></i> Cheque'
                };
                return formas[data.forma_pago] ?? data.forma_pago;
            }
        },
        { 
            data: 'monto', 
            title: 'Monto',
            render: (data) => '$' + parseFloat(data).toLocaleString('es-MX', { minimumFractionDigits: 2 })
        },
        { 
            data: null, 
            title: 'Estatus',
            render: function(data){
                return data.estatus ===1
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-danger">Cancelado</span>';
            }
        },
        { 
            data: null, 
            title: 'Comprobante',
            render: function(data){
                if(data.comprobante_archivo){
                    return `<a href="${BASE_URL}${data.comprobante_archivo}" target="_blank" class="btn btn-sm" style="background-color:tomato; color:white;">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>`;
                }
                return '<span class="text-muted">Sin archivo</span>';
            }
        },
        {
            data: null, 
            title: 'Opciones',
            render: function(data){
                let btn_cancel = data.estatus === 1
                    ? `<div class="btn btn-warning" onclick="cancelarGasto(${data.id}, 'cancelado')" title="Cancelar gasto">
                            <i class="fa-solid fa-ban"></i>
                       </div>`
                    : `<div class="btn btn-info" onclick="cancelarGasto(${data.id}, 'activo')" title="Reactivar gasto">
                            <i class="fa-solid fa-rotate-left"></i>
                       </div>`;

                return `<div class="d-flex gap-1">
                            <div class="btn btn-primary" onclick="editarGasto(${data.id})" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                            ${btn_cancel}
                        </div>`;
            }
        }
    ];

    const ajaxConfig = {
        url: BASE_URL + 'api/gastos.php?tipo=datatable',
        type: 'POST',
        data: function(d){
            d.folio       = $('#f-folio').val();
            d.ciclo       = $('#f-ciclo').val();
            d.subcategoria   = $('#f-subcategoria').val();
            d.forma_pago  = $('#f-forma-pago').val();
            d.comprobante = $('#f-comprobante').val();
            d.inicio      = $('#f-fecha-inicio').val();
            d.fin         = $('#f-fecha-fin').val();
            d.estatus     = $('#f-estatus').val();
        }
    };

    table = initCustomDataTable('#gastos', ajaxConfig, columns), [[1, 'desc']] ;
    GeneralEventListener('btn-buscar', 'click', reloadTable);

    function reloadTable(){
        table.ajax.reload(null, false);
    }
});

  function mostrarRecibo(id_recibo){
    window.open(BASE_URL + 'recibos/normal-pdf/'+id_recibo,'_blank'); 
  }

  function cancelarGasto(id_gasto, tipo_cancelacion){
    let preg;
    if(tipo_cancelacion=='cancelado'){
      preg =  '¿Deseas cancelar este gasto?'
    }else{
      preg =  '¿Deseas descancelar este gasto?'
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
          url:  BASE_URL + "api/gastos.php?tipo=actualizar_estatus_gasto",
          data: {'id_gasto':id_gasto, 'tipo_cancelacion': tipo_cancelacion},
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

  function editarGasto(id, folio){
    window.open(BASE_URL + 'gastos/editar/' + id, '_blank');
    }

  //Exposición global
  window.mostrarRecibo = mostrarRecibo; 
  window.cancelarGasto = cancelarGasto; 
  window.editarGasto = editarGasto; 
 


  
  GeneralEventListener('btn-limpiar-filtros', 'click', function () {
    $('#f-folio').val('');
    $('#f-ciclo').selectpicker('val', '');
    $('#f-categoria').selectpicker('val', '');
    $('#f-forma-pago').selectpicker('val', '');
    $('#f-comprobante').selectpicker('val', '');
    $('#f-fecha-inicio').val('');
    $('#f-fecha-fin').val('');
    $('#f-estatus').selectpicker('val', '');
    table.ajax.reload(null, false);
});
  