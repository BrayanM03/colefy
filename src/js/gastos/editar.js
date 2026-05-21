import { verificarCampos, estilizarBordes } from './verificar.js';

function actualizarGasto(id_gasto) {

        console.log(id_gasto);
        const btn_update = document.getElementById('btn-actualizar-gasto')
        btn_update.disabled = true;
        btn_update.textContent = 'Realizando...'; // O mostrar un spinner

        let verify = verificarCampos();
        if (verify) {
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

            data.append('id_gasto', id_gasto)

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
                data.append('comprobante', ''); // Sin archivo, manda vacío
            }
            data.append('formas_pago', forma_pago)


            $.ajax({
                type: "post",
                processData: false,
                contentType: false,
                url: BASE_URL + "api/gastos.php?tipo=actualizar_gasto",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.estatus) {
                        //window.open('./recibos/pdf/'+response.data.id_recibo,'_blank');

                        Swal.fire({
                            icon: 'success',
                            title: response.mensaje,
                            confirmButtonText: 'Entendido'
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.mensaje,
                            confirmButtonText: 'Entendido'

                        })
                    }
                }
            }).always(function() {
                btn_update.disabled = false;
                btn_update.textContent = 'Actualizar gasto';
            });

        } else {
            btn_update.disabled = false;
            btn_update.textContent = 'Actualizar gasto';
        }
}


window.actualizarGasto = actualizarGasto;