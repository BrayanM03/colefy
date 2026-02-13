function mostrarCredenciales(id_usuario){
    let usuario_actual = document.getElementById("usuario").value;
  
    Swal.fire({
        html: `
        <div class="container">
        <div class="row">
            <b><h3>Actualizar credenciales</h3></b>
        </div>

            <div class="row mt-3">
                <div class="col-7">
                    <label>Usuario</label>
                    <input type="text" id="usuario_nuevo" class="form-control" value="${usuario_actual}" placeholder="usuario" autocomplete="nope">
                </div>
                <div class="col-5">
                    <div id="btn-user" onclick="actualizarNombreUsuario(${id_usuario}, '${usuario_actual}')" style="margin-top:20px;" class="btn btn-secondary">Actualizar</div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-7">
                    <label>Nueva contraseña</label>
                    <input type="password" id="password_nuevo" class="form-control" placeholder="*****" autocomplete="new-password">
                </div>
                <div class="col-5">
                    <div id="btn-password" onclick="actualizarContrasenaUsuario(${id_usuario})" style="margin-top:20px;" class="btn btn-secondary">Actualizar</div>
                </div>
            </div>
        </div>
        `,
        
        confirmButtonText : `Cerrar`,
    })
}


function actualizarNombreUsuario(id_usuario, usuario_actual){

    $("#btn-user").empty().append(`
    <div class='preloader'></div>
    `)

    let usr = $("#usuario_nuevo").val()
    if(usr == ""|| usr == null){
        Swal.showValidationMessage(
            `Escribe una constraseña`
          )

          $("#btn-user").empty().append(`
                    Actualizar
                 `)  
    }else{
        let usuario_nuevo = document.getElementById("usuario_nuevo").value;

        $.ajax({
            type: "POST",
            url: "../servidor/usuarios/actualizar-credenciales.php",
            data: {"tipo": "usuario" ,"id_usuario": id_usuario, "usuario_nuevo": usuario_nuevo, "usuario_actual": usuario_actual},
            dataType: "JSON",
            success: function (response) {
                if(response.estatus == true) {


                setInterval(function () {
                    $("#btn-user").empty().removeClass("btn-secondary").addClass("btn-success").append(`
                    Guardado
                 `)

                 reloadInformacionUsuario()
                }, 1500)
                
            }else{

                alert(response.mensaje);
                $("#btn-user").empty().append(`
                    Actualizar
                 `)                   
            }
            }
        });
    }
    
}


function actualizarContrasenaUsuario(id_usuario){
    $("#btn-password").empty().append(`
    <div class='preloader'></div>
    `)
    let pass = $("#password_nuevo").val()

    if(pass == ""|| pass == null){
        Swal.showValidationMessage(
            `Escribe un usuario`
          )

          $("#btn-password").empty().append(`
                    Actualizar
                 `)  
    }else{
        let password = document.getElementById("password_nuevo").value;

        $.ajax({
            type: "POST",
            url: "../servidor/usuarios/actualizar-credenciales.php",
            data: {"tipo": "password", "id_usuario": id_usuario, "password": password},
            dataType: "JSON",
            success: function (response) {
                if(response.estatus == true) {

                setInterval(function () {
                    $("#btn-password").empty().removeClass("btn-secondary").addClass("btn-success").append(`
                    Guardado
                     `);
                     reloadInformacionUsuario()
                }, 1500)
                

                    
            }else{
                alert(response.mensaje);
                $("#btn-password").empty().append(`
                    Actualizar
                 `)                  
            }
            }
        });
    }
}