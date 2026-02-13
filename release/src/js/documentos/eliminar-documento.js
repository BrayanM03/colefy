function eliminarRegistro(registro_id, tipo, nombre_){

    Swal.fire({
        html: `
        ¿Deseas eliminar este ${nombre_}?<br>
        `,
        allowOutsideClick: true,
        confirmButtonText: "Si",
        showCancelButton: true,
        cancelButtonText: "Mejor no"
        
    }).then((res)=>{
        if(res.isConfirmed){
            $.ajax({
                type: "POST",
                url: "../servidor/documentos/borrar-registro.php",
                data: {id_documento:registro_id, tipo},
                dataType: "JSON",
                success: function (response) {
                    if(response.estatus == true){
                        Swal.fire({
                            icon: 'success',
                            html: `
                           ${nombre_} eliminado<br>
                            `,
                            allowOutsideClick: true,
                            confirmButtonText: "Entendido",
                            showCancelButton: false,
                            
                        }).then((r)=>{
                            tabla.ajax.reload(null, false);
                            
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
                                tabla.ajax.reload(null, false);
                            }
                        })
                    }

                    
                }
            });
        }
    })

    

}



/* const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  }) */