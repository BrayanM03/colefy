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


function verificarCampos(tipo){
    let fecha = $('#fecha').val()
    let ciclo = $('#ciclo').val()
    let proveedor = $('#proveedor').val()
    let tipo_comprobante = $("#tipo_comprobante").val()
    let categoria = $("#categorias-gastos").val()
    let concepto = $("#concepto").val()
    let forma_pago = $("#forma_pago").val()
    let monto = $("#monto").val()

    const campos = [
        { id: 'fecha',             valor: fecha,            mensaje: 'Selecciona una fecha'          },
        { id: 'ciclo',             valor: ciclo,            mensaje: 'Selecciona un ciclo'           },
        { id: 'proveedor',         valor: proveedor,        mensaje: 'Selecciona un proveedor'       },
        { id: 'tipo_comprobante',  valor: tipo_comprobante, mensaje: 'Selecciona un tipo'            },
        { id: 'categorias-gastos', valor: categoria,        mensaje: 'Selecciona una categoría'      },
        { id: 'concepto',          valor: concepto,         mensaje: 'Escribe un concepto'           },
        { id: 'forma_pago',        valor: forma_pago,       mensaje: 'Selecciona una forma de pago'  },
        { id: 'monto',             valor: monto,            mensaje: 'Ingresa un monto'              },
      ]
      
      for (const campo of campos) {
        if (!campo.valor) {
          Toast.fire({ icon: 'warning', title: campo.mensaje })
          estilizarBordes(campo.id, campo.mensaje + '.', false)
          return false
        }
      }
      return true;
  }

  function estilizarBordes(id, mensaje, estatus){
    if(!estatus){
      $(`#${id}`).css('border', '1px solid red')                 
      let hermano= $(`#${id}`).next()
      if (!hermano.is("small")) {
          hermano.removeClass('border-success').addClass('border-red')
        }
    }else{
      $(`#${id}`).css('border', '1px solid #59de64') 
      let hermano= $(`#${id}`).next()
      if (!hermano.is("small")) {
        hermano.removeClass('border-red').addClass('border-success')
      }
    }
    $(`#small_${id}`).text(mensaje)
    $(`#${id}`).selectpicker('refresh')
  }

  export { verificarCampos, estilizarBordes, Toast };
