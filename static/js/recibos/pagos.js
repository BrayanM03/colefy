 //Funciones para añadir un nuevo pago

 import {GeneralEventListener} from '../utils/listeners.js';
 import { retornarRecibo } from './actualizar-recibo.js';
 import { estilizarBordes, tabla_pagos } from './actualizar-recibo.js';

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

 GeneralEventListener('btn-nuevo-pago', 'click', nuevoPago)

 function nuevoPago(){
    // Recuperar el ID en JS
    const contenedor = document.getElementById('contenedor-ids-recibo');
    const id_recibo = contenedor.dataset.idRecibo;
    const folio = contenedor.dataset.folioInterno;
    const input_saldo_pendiente_datos_generales = document.getElementById('saldo-pendiente')
    const input_saldo_pendiente_pagos = document.getElementById('restante')

    let comentarios;
    let forma_pago;
   

    Swal.fire({
        title: 'Nuevo pago REC-'+ folio,
        html: `
        <div class="container">
        <div class="row">
            <div class="col-12">
                <label>Forma de pago</label>
                <select id="forma-pago" class="mt-2 form-control selectpicker"
                        placeholder="Selecciona una forma de pago"
                        data-live-search="true"
                        multiple>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Deposito">Deposito</option>
                    <option value="Cheque">Cheque</option>
                </select>
                <small id="small_forma-pago" style="color:tomato;"></small>
            </div>
    
            <div class="col-12 col-md-12">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="area-formas-pago mb-3" id="area-formas-pago">
                            <label><b>Montos de la forma de pago</b></label><br>
                            <div class="row mt-3" id="lista-formas-pago">
                                <div class="col-12 text-center">
                                    Sin formas de pago seleccionadas
                                </div>
                            </div>
                        </div>
                        <small id="small_area-formas-pago" style="color:tomato;"></small>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="row">
                    <div class="col-12">
                        <h3>📊 Resumen de Pago</h3>
                    </div>
                    <div class="row mb-4">
                    <div class="col-md-4">
                        <label>Monto:</label>
                        <input type="text" class="form-control" id="monto-sumatoria-pagos" readonly placeholder="0.00">
                        <small id="small_monto-sumatoria-pagos" style="color:tomato;"></small>

                    </div>
                    <div class="col-md-4">
                        <label>Importe total:</label>
                        <input type="text" class="form-control" id="importe-total-pagos" readonly placeholder="0.00">
                    </div>
                    <div class="col-md-4">
                        <label>Restante:</label>
                        <input type="text" class="form-control" id="restante-pagos" readonly placeholder="0.00">
                    </div>
                    </div>
                    <hr>
                   
            </div>
            <div class="col-12 mt-4">
                <label class="mb-2" for="comentarios">💬 Comentarios</label>
                <textarea class="form-control" id="comentarios" rows="3" placeholder="Añade comentarios adicionales sobre el pago"></textarea>
                </div>
            </div>
    </div>
        `,
        height: '300px',
        // --- INICIALIZACIÓN CRUCIAL DENTRO DE didOpen ---
        didOpen: function(){
             let formas_pago_area = document.getElementById('area-formas-pago');

            // 1. OBTENER EL CONTENEDOR DE SWEETALERT2
            const swalContainer = Swal.getPopup();

            // 2. INICIALIZAR el Selectpicker y decirle que renderice DENTRO del modal
            $("#forma-pago").selectpicker({
                // Esta opción le indica a Selectpicker que ancle su dropdown al modal, 
                // resolviendo los problemas de z-index y foco.
                dropdownParent: swalContainer 
            });

            // Nota: El llamado a .selectpicker('refresh') ya no es necesario aquí, 
            // pues la inicialización ya lo hace.
            let recibo = retornarRecibo()
            $("#importe-total-pagos").val(recibo.monto_total)
            $("#restante-pagos").val(recibo.saldo_pendiente)
            const forma_pago_select = document.getElementById('forma-pago');
            if (forma_pago_select) forma_pago_select.addEventListener('change', setearFormaPago);

            formas_pago_area.addEventListener('keyup', (evento) => {
              
                // 🔑 CLAVE: Filtra el evento para asegurarte de que vino de la clase específica
                // Esto comprueba si el elemento que originó el evento tiene la clase 'input-monto-pago'
                if (evento.target.matches('.input-forma-pago')) {
                    // Si sí, llama a tu función de lógica
                    setearMonto(evento);
                }
            });

        },
        preConfirm: ()=>{
          
            const f_pago = $("#forma-pago").val()
           
            if(f_pago.length == 0){
                Swal.showValidationMessage(`
                Selecciona al menos una forma de pago.
              `);
            }else{
                const monto_suma = parseFloat(document.getElementById('monto-sumatoria-pagos').value)
                const rest =  parseFloat(document.getElementById('restante-pagos').value)
            
                if(monto_suma > rest){
                    Swal.showValidationMessage(`
                    La suma no puede ser mayor que el restante.
                  `);
                }else if (monto_suma <= 0){
                    Swal.showValidationMessage(`
                    La suma no puede ser igual o menor a 0.
                  `);
                }  
            }          
        },
        confirmButtonText: 'Registrar',
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        showCloseButton: true
    }).then((r)=>{
        if(r.isConfirmed){
            forma_pago = $("#forma-pago").val()
            comentarios = document.getElementById('comentarios').value
            const data = new FormData();
                   

            data.append('id_recibo', id_recibo)
           
            data.append('formas_pago',  forma_pago)
            data.append('comentarios', comentarios)
        
            if(forma_pago.length > 0){
              forma_pago.forEach(element => {
                let forma =$("#f_"+element).val()
                data.append(element, forma)
              });
            }
           

            Swal.fire({
                title: '¿Confirmas realización del pago?',
                confirmButtonText: 'Realizar',
                cancelButtonText: 'Cancelar',
                showCloseButton: true, 
                showCancelButton: true,
            }).then((re)=>{
                if(re.isConfirmed){
                    
                    // ⚠️ PRUEBA: AJAX simplificado
                    $.ajax({
                        type: "POST",
                        url: BASE_URL +"api/recibos.php?tipo=realizar_pago",
                        data: data,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        beforeSend: function(){
                            console.log('⏳ beforeSend ejecutado');
                        },
                        success: function (response) {
                            if(response.estatus){
                                Swal.fire({
                                    title: response.mensaje,
                                    icon: 'success',
                                    confirmButtonText: 'Entendido',
                                    showCloseButton: true
                                })
                                input_saldo_pendiente_datos_generales.value = response.data.nuevo_saldo
                                input_saldo_pendiente_pagos.value = response.data.nuevo_saldo
                                document.getElementById('pagado').value = (parseFloat(response.data.recibo.monto_total) - parseFloat(response.data.recibo.saldo_pendiente));
                                const select_estatus = $("#estatus")
                                select_estatus.val(response.data.recibo.estatus)
                                select_estatus.selectpicker('refresh')
                                tabla_pagos.ajax.reload(null, false)
                                if(response.data.recibo.saldo_pendiente==0) document.getElementById('btn-nuevo-pago').classList.add('d-none') //Ocultamos btn nuevo pago en caso de que el recibo ya este pagado

                                
                            }else{
                                Swal.fire({
                                    title: response.mensaje,
                                    icon: 'error',
                                    confirmButtonText: 'Entendido',
                                    showCloseButton: true
                                })
                            }


                        },
                        error: function(xhr, status, error) {
                            console.error('❌ ERROR:', {xhr, status, error});
                            console.error('Response text:', xhr.responseText);
                        },
                        complete: function(){
                            console.log('🏁 AJAX completado (success o error)');
                        }
                    });
                    
                    console.log('📡 AJAX llamado (después de $.ajax)');
                    
                }
            })

        }
    })
 }

export function cancelarPago(id_pago, tipo_cancelacion){
    const data = new FormData();
    data.append('id_pago', id_pago)
    data.append('tipo_cancelacion', tipo_cancelacion)
    const input_saldo_pendiente_datos_generales = document.getElementById('saldo-pendiente')
    const input_saldo_pendiente_pagos = document.getElementById('restante')
    const select_estatus = $("#estatus")
   
    Swal.fire({
        title: '¿Confirmas cancelación del pago?',
        confirmButtonText: 'Cancelar',
        cancelButtonText: 'Mejor no',
        showCloseButton: true, 
        showCancelButton: true,
    }).then((re)=>{
        if(re.isConfirmed){
            $.ajax({
                type: "POST",
                url: BASE_URL + "api/recibos.php?tipo=cancelar_pago",
                data: data,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    if(response.estatus){
                        Toast.fire({icon: "success", title: response.mensaje});
                        input_saldo_pendiente_datos_generales.value = response.data.saldo_pendiente
                        input_saldo_pendiente_pagos.value = response.data.saldo_pendiente
                        if(response.data.saldo_pendiente>0){
                            document.getElementById('btn-nuevo-pago').classList.remove('d-none')
                        } //Ocultamos btn nuevo pago en caso de que el recibo ya este pagado

                        select_estatus.val(response.data.estatus)
                        document.getElementById('pagado').value = (parseFloat(response.data.monto_total) - parseFloat(response.data.saldo_pendiente));

                        select_estatus.selectpicker('refresh')
                        tabla_pagos.ajax.reload(null, false)

                    }else{
                        Toast.fire({icon: "error", title: response.mensaje});

                    }
                }
            })
        }
    })
 }

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  }
  
function setearFormaPago(){
    let forma_pago = $("#forma-pago").val()
    let area = $("#area-formas-pago")
    $("#monto-sumatoria-pagos").val('')
    area.empty()
    if(forma_pago.length > 0){
        area.append(`
        <label><b>Montos de la forma de pago</b></label><br>
        <div class="row mt-3" id="lista-formas-pago"></div>`)
        forma_pago.forEach(element => {
            $("#lista-formas-pago").append(`
                <div class="col-12 col-md-6 mb-2">
                    <div class="row mb-1" style="vertical-align:middle !important;">
                        <div class="col-1">
                            <img src="${BASE_URL}/static/img/formas_pago/${element}.png" style="width: 30px">
                        </div>
                        <div class="col-10" style="margin-left: 7px; vertical-align:middle !important;">
                            <label>${element}</label>
                        </div>
                    </div>
                    <input class="form-control input-forma-pago" id="f_${element}" type="number" placeholder="0.00">
                </div>
            `)
        });

        estilizarBordes('forma-pago', '', true)
        estilizarBordes('monto-sumatoria-pagos', '', false)

    }else{
        estilizarBordes('forma-pago', 'Selecciona al menos una forma de pago', false)
        area.append(`
        <label><b>Montos de la forma de pago</b></label><br>

        <div class="row mt-3" id="lista-formas-pago">
            <div class="col-12 col-md-12 text-center">
                Sin formas de pago seleccionadas
           </div>
        </div>
        `)
    }
        
}

function setearMonto(e){
    let area = document.getElementById('area-formas-pago');
    let inputs = area.querySelectorAll('input[type="number"]')
    let importe = parseFloat($("#importe-total-pagos").val());
    let restante = parseFloat($("#restante-pagos").val());
    let suma =0;
    inputs.forEach(element => {
      let valor = parseFloat(element.value) || 0; // si está vacío, que sea 0
      suma += parseFloat(valor);
    });

    $("#monto-sumatoria-pagos").val(suma)
    if(suma > 0 && suma <= restante){
        estilizarBordes('monto-sumatoria-pagos', '', true)
        estilizarBordes('area-formas-pago', '', true)
  
      }else if(suma <= 0){

        estilizarBordes('area-formas-pago', '', false)

        estilizarBordes('monto-sumatoria-pagos', 'La suma no puede ser igual o menor a 0.', false)
      }else if(suma > restante){
        estilizarBordes('area-formas-pago', '', false)

        estilizarBordes('monto-sumatoria-pagos', 'La suma no puede ser mayor al restante.', false)
      }
  }

  
 