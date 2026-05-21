import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
import { verificarCampos, estilizarBordes, Toast } from './verificar.js';
GeneralEventListener('btn-guardar-gasto', 'click', registrarGasto)


function registrarGasto(){
    const btn_hacer_recibo = document.getElementById('btn-guardar-gasto')
    btn_hacer_recibo.disabled = true;
    btn_hacer_recibo.textContent = 'Realizando...'; // O mostrar un spinner
  
    let verify = verificarCampos();
     if(verify){
      const data = new FormData();

    let fecha = $('#fecha').val()
    let ciclo = $('#ciclo').val()
    let proveedor = $('#proveedor').val()
    let tipo_comprobante = $("#tipo_comprobante").val()
    let categoria = $("#categorias-gastos").val()
    let concepto = $("#concepto").val()
    let forma_pago = $("#forma_pago").val()
    let monto = $("#monto").val()
    let comprobante = $("#comprobante_file").val()
    let observaciones = $("#observaciones").val()


  data.append('fecha', fecha)
  data.append('proveedor', proveedor)
  data.append('ciclo', ciclo)
  data.append('tipo_comprobante', tipo_comprobante)
  data.append('categoria', categoria)
  data.append('concepto', concepto)
  data.append('monto', monto)
  data.append('observaciones', observaciones)
  const archivoInput = document.getElementById('comprobante_file');
  const archivo = archivoInput.files[0]; // El archivo real
  
  if (archivo) {
      data.append('comprobante', archivo); // Manda el File object
  } else {
      data.append('comprobante', '');      // Sin archivo, manda vacío
  }
    data.append('formas_pago', forma_pago)

  /* if(forma_pago.length > 0){
    forma_pago.forEach(element => {
      let forma =$("#f_"+element).val()
      data.append(element, forma)
    });
  } */

  $.ajax({
    type: "post",
    processData: false,
    contentType: false,  
    url: BASE_URL + "api/gastos.php?tipo=nuevo_gasto",
    data: data,
    dataType: "json",
    success: function (response) {
      if(response.estatus){
        //window.open('./recibos/pdf/'+response.data.id_recibo,'_blank');

        Swal.fire({
          icon: 'success',
          title: response.mensaje,
          confirmButtonText: 'Entendido'
        })
      }else{
        Swal.fire({
          icon: 'error',
          title: response.mensaje, 
          confirmButtonText: 'Entendido'

        })
      }
    }
  }).always(function(){
    btn_hacer_recibo.disabled = false;
    btn_hacer_recibo.textContent = 'Registrar gasto'; 
  });
 }else{
  btn_hacer_recibo.disabled = false;
  btn_hacer_recibo.textContent = 'Registrar gasto'; 
 }

   }


