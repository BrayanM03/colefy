function EnviarTicketAbono(documento_id, usuario_id, orden_id) {
  var nombre_cliente = false;
  Swal.fire({
    html: `
        <div class="container">
        <h3>Enviar correo del recibo</h3>
            <div class="row">
            <div class="col-12 col-md-12">
                <span>Porfavor, selecciona un correo</span>
                <select class="form-control" id="correo-cliente">
                </select>
            </div>
            </div>
        </div>
        `,
    didRender: function () {
      var related_tables = {
        "01": {
          nombre: "detalle_correo",
        },
      };
      var data = {
        id: usuario_id,
        tabla: "clientes",
        relacion: true,
        tablas_relacionadas: related_tables,
      };
      fetch("../servidor/database/traer-un-solo-dato.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then((response) => response.json())
        .then((data) => {
          sel_correo = $("#correo-cliente");
          sel_correo.empty();
          nombre_cliente = data.data.nombre;
          if (data.detalle_correo.length > 0) {
            data.detalle_correo.forEach((el) => {
              sel_correo.append(
                `<option value="${el.correo}">${el.correo}</option`
              );
            });
          } else {
            sel_correo.append(`<option value="null">Sin correos</option`);
          }
        });
    },
    confirmButtonText: "Enviar",
  }).then((resp) => {
    if (resp.isConfirmed) {
      let correo = document.getElementById("correo-cliente").value;
      Swal.fire({
        timer: 3000,
        html: `
                <div class="container">
                <div class="row justify-content-center">
                    <dotlottie-player src="https://assets7.lottiefiles.com/packages/lf20_xumyfzqp.json" background="transparent"  speed="1"  style="width: 150px; height: 150px;" loop autoplay></dotlottie-player></br>
                </div>
                <span>Enviando correo...</span>
                </div>
                `,
        showConfirmButton: false,
      }).then((r) => {
        let path = "../../static/docs/C" + usuario_id;
            var data_post = {
            id_doc: documento_id, prefix: "TICKET", 'path': path
          };
          fetch("../servidor/database/validar-documentos.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data_post),
          }).then(response => response.json())
            .then(data => {
                console.log(data);
                if(!data.estatus){
                    guardarTicketAbono(documento_id)
                }else{
                    $.ajax({
                        type: "POST",
                        url: "../servidor/correo/enviar-correo-ticket.php",
                        data: {
                          nombre_cliente: nombre_cliente,
                          correo_cliente: correo,
                          cliente_id: usuario_id,
                          abono_id: documento_id,
                        },
                        dataType: "JSON",
                        success: function (response2) {
                          if (response2.estatus == false) {
                            Swal.fire({
                              icon: "error",
                              html: `<h3>${response2.mensaje}</h3>`,
                            });
                          } else {
                            Swal.fire({
                              icon: "success",
                              html: `<h3>${response2.mensaje}</h3>`,
                            });
                          }
                        },
                      }).fail( function() {

                        alert( 'Error al mandar correo!!' );
                    
                    });
                }
            })
      
        /*$.ajax({
            type: "POST",
            url: "../servidor/database/validar-documentos.php",
            data: { id_doc: documento_id, prefix: "TICKET", 'path': path },
            dataType: "JSON",
            success: function (validacion) {
                if(!validacion.estatus){
                    guardarTicketAbono(documento_id)
                }else{
                    $.ajax({
                        type: "POST",
                        url: "../servidor/correo/enviar-correo-ticket.php",
                        data: {
                          nombre_cliente: nombre_cliente,
                          correo_cliente: correo,
                          cliente_id: usuario_id,
                          abono_id: documento_id,
                        },
                        dataType: "JSON",
                        success: function (response2) {
                          if (response2.estatus == false) {
                            Swal.fire({
                              icon: "error",
                              html: `<h3>${response2.mensaje}</h3>`,
                            });
                          } else {
                            Swal.fire({
                              icon: "success",
                              html: `<h3>${response2.mensaje}</h3>`,
                            });
                          }
                        },
                      }).fail( function() {

                        alert( 'Error al mandar correo!!' );
                    
                    });
                }
            }}).fail( function() {

                        alert( 'Error al validar documentos!!' );
                    
                    });*/
        
      });
    }
  });
}

function EnviarContrato(documento_id, usuario_id, orden_id) {
  var nombre_cliente = false;
  Swal.fire({
    html: `
        <div class="container">
        <h3>Enviar correo del contrato</h3>
            <div class="row">
            <div class="col-12 col-md-12">
                <span>Porfavor, selecciona un correo</span>
                <select class="form-control" id="correo-cliente">
                </select>
            </div>
            </div>
        </div>
        `,
    didRender: function () {
      var related_tables = {
        "01": {
          nombre: "detalle_correo",
        },
      };
      var data = {
        id: usuario_id,
        tabla: "clientes",
        relacion: true,
        tablas_relacionadas: related_tables,
      };
      fetch("../servidor/database/traer-un-solo-dato.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then((response) => response.json())
        .then((data) => {
          sel_correo = $("#correo-cliente");
          sel_correo.empty();
          nombre_cliente = data.data.nombre;
          if (data.detalle_correo.length > 0) {
            data.detalle_correo.forEach((el) => {
              sel_correo.append(
                `<option value="${el.correo}">${el.correo}</option`
              );
            });
          } else {
            sel_correo.append(`<option value="null">Sin correos</option`);
          }
        });
    },
    confirmButtonText: "Enviar",
  }).then((resp) => {
    if (resp.isConfirmed) {
      let correo = document.getElementById("correo-cliente").value;
      Swal.fire({
        timer: 3000,
        html: `
                <div class="container">
                <div class="row justify-content-center">
                    <dotlottie-player src="https://assets7.lottiefiles.com/packages/lf20_xumyfzqp.json" background="transparent"  speed="1"  style="width: 150px; height: 150px;" loop autoplay></dotlottie-player></br>
                </div>
                <span>Enviando correo...</span>
                </div>
                `,
        showConfirmButton: false,
      }).then(() => {
        //Validacion de documento
        let path = "../../static/docs/C" + usuario_id;
        var data_post = {
          id_doc: documento_id, prefix: "CONTRATO", 'path': path
        };
        fetch("../servidor/database/validar-documentos.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data_post),
        }).then(response => response.json())
          .then(data => {
            if (!data.estatus) {
              guardarContrato(
                documento_id,
                orden_id,
                correo,
                nombre_cliente,
                usuario_id
              );

              Swal.fire({
                icon: "success",
                html: `<h3>Correo enviado</h3>`,
              });

            } else {
              $.ajax({
                type: "POST",
                url:
                  "../servidor/correo/enviar-correo.php?folio=" + documento_id,
                data: {
                  nombre_cliente: nombre_cliente,
                  correo_cliente: correo,
                  cliente_id: usuario_id,
                  abono_id: documento_id,
                },
                dataType: "JSON",
                success: function (response2) {
                  if (response2.estatus == false) {
                    Swal.fire({
                      icon: "error",
                      html: `<h3>${response2.mensaje}</h3>`,
                    });
                  } else {
                    Swal.fire({
                      icon: "success",
                      html: `<h3>${response2.mensaje}</h3>`,
                    });
                  }
                },
              });
            }
          });
        /* $.ajax({
          type: "POST",
          url: "../servidor/database/validar-documento.php",
          data: { id_doc: documento_id, prefix: "CONTRATO", path: path },
          dataType: "JSON",
          success: function (validacion) {
            if (!validacion.estatus) {
              guardarContrato(
                documento_id,
                orden_id,
                correo,
                nombre_cliente,
                usuario_id
              );

              Swal.fire({
                icon: "success",
                html: `<h3>Correo enviado</h3>`,
              });

            } else {
              $.ajax({
                type: "POST",
                url:
                  "../servidor/correo/enviar-correo.php?folio=" + documento_id,
                data: {
                  nombre_cliente: nombre_cliente,
                  correo_cliente: correo,
                  cliente_id: usuario_id,
                  abono_id: documento_id,
                },
                dataType: "JSON",
                success: function (response2) {
                  if (response2.estatus == false) {
                    Swal.fire({
                      icon: "error",
                      html: `<h3>${response2.mensaje}</h3>`,
                    });
                  } else {
                    Swal.fire({
                      icon: "success",
                      html: `<h3>${response2.mensaje}</h3>`,
                    });
                  }
                },
              });
            }
          },
        }); */
      });
    }
  });
}
