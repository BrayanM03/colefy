/* reloadInformacionUsuario()
function reloadInformacionUsuario(){

    let id_usuario = obtenerParametroGet('id')
    fetch('../servidor/database/traer-un-solo-dato.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({"tabla": "usuarios", "id": id_usuario, "relacion": false, "tablas_relacionadas":false})
        })
        .then(response => response.json())
        .then((data) =>{
            document.getElementById('nombre').value = data.data.nombre
            document.getElementById('apellido').value = data.data.apellido
            document.getElementById('sucursal').value = data.data.sucursal_id
            document.getElementById('rol').value = data.data.rol
            document.getElementById('usuario').value = data.data.usuario
    
            $("#sucursal").val(data.data.sucursal_id)
            let id_sucursal = $("#sucursal").val()
            setProyectos(id_sucursal, data.data.proyecto_id)
    
        });
} */
 

function editarSolicitud(id_solicitud, mostrarAprobar=true){
    console.log(mostrarAprobar);
    $.ajax({
        type: "post",
        url: "../servidor/solicitudes/traer-solicitud.php",
        data: {id_solicitud},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                let fecha_cumple = response.datos.fecha_cumple;
                let edad = calcularEdad(fecha_cumple);
                Swal.fire({
                    didOpen: function(){
                        $("#sexo").val(response.datos.sexo);
                    },
                    width:'650px',
                    html:`
                    <h2 class="mb-4">Formulario de Registro</h2>
                    <form id="formulario-solicitante">
                        <div class="row">
                            <div class="col-md-8">
                            <!-- Nombre -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="${response.datos.nombre}" placeholder="Nombre completo" required>
                                </div>
                            </div>
                        
                            <!-- Edad -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edad" class="form-label">Edad</label>
                                    <input type="number" value="${edad}" class="form-control" id="edad" placeholder="Edad" required>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-6 col-12">
                            <!-- Nombre -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Apellido Paterno</label>
                                    <input type="text" class="form-control" id="apellido_paterno" value="${response.datos.apellido_paterno}" placeholder="Nombre completo" required>
                                </div>
                            </div>
                        
                            <!-- Edad -->
                            <div class="col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="edad" class="form-label">Apellido Materno</label>
                                    <input type="text" value="${response.datos.apellido_materno}" class="form-control" id="apellido_materno" placeholder="Edad" required>
                                </div>
                            </div>
                        </div>
                          <!-- Sexo -->
                        <div class="row">
                          <div class="col-md-4">  
                              <div class="mb-3">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select class="form-select" id="sexo" required>
                                <option selected disabled>Selecciona una opción</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                </select>
                            </div>
                          </div>
                          <div class="col-md-4">  
                          <!-- País -->
                            <div class="mb-3">
                                <label for="pais" class="form-label">País</label>
                                <input type="text"  value="${response.datos.pais}" class="form-control" id="pais" placeholder="País de residencia" required>
                            </div>
                          </div>
                          <div class="col-md-4">  
                          <!-- País -->
                            <div class="mb-3">
                                <label for="pais" class="form-label">Fecha Nac.</label>
                                <input type="date"  value="${response.datos.fecha_cumple}" class="form-control" id="fecha_cumple">
                            </div>
                          </div>
                        </div>
                    
                          
                    
                          <!-- Estado -->
                          <div class="row">
                             <div class="col-md-6"> 
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <input type="text" value="${response.datos.estado}" class="form-control" id="estado" placeholder="Estado" required>
                                </div>
                            </div>
                            <div class="col-md-6"> 
                                <!-- Ciudad -->
                                <div class="mb-3">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" value="${response.datos.ciudad}" id="ciudad" placeholder="Ciudad" required>
                                </div>
                            </div>
                          </div>
                    
                          <div class="row">
                             <div class="col-md-6">
                                <!-- Teléfonos -->
                                <div class="mb-3">
                                     <label for="telefonos" class="form-label">Teléfonos</label>
                                     <input type="text" value="${response.datos.telefono}" class="form-control" id="telefono" placeholder="Teléfonos de contacto" required>
                                </div>
                             </div>
                             <div class="col-md-6">
                                <!-- Teléfonos -->
                                <div class="mb-3">
                                    <label for="telefonos" class="form-label">Teléfono casa</label>
                                    <input type="text" value="${response.datos.telefono_casa}" class="form-control" id="telefono_casa" placeholder="Teléfonos de contacto" required>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <!-- Correo -->
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" value="${response.datos.correo}" class="form-control" id="correo" placeholder="Correo electrónico" required>
                                </div>
                             </div>
                          </div> 
                          <hr>
                          <h3 class="ml-auto mb-3"><b>Datos de la iglesia</b></h3>
                          <!-- Iglesia -->
                          <div class="mb-3">
                            <label for="iglesia" class="form-label">Iglesia</label>
                            <input type="text" value="${response.datos.nombre_iglesia}" class="form-control" id="nombre_iglesia" placeholder="Nombre de la Iglesia" required>
                          </div>
                    
                          <!-- Pastor -->
                          <div class="mb-3">
                            <label for="pastor" class="form-label">Pastor</label>
                            <input type="text" value="${response.datos.nombre_pastor}" class="form-control" id="nombre_pastor" placeholder="Nombre del Pastor" required>
                          </div>
                    
                         
                    
                          <div class="row">
                             <div class="col-md-6">
                             <!-- Teléfono Pastor -->
                             <div class="mb-3">
                               <label for="telefono_pastor" class="form-label">Teléfono del Pastor</label>
                               <input type="text" value="${response.datos.telefono_pastor}" class="form-control" id="telefono_pastor" placeholder="Teléfono del Pastor" required>
                             </div>
                            </div>
                            <div class="col-md-6">
                            <!-- Curso de interés -->
                            <div class="mb-3">
                                <label for="curso_interes" class="form-label">Curso de Interés</label>
                                <input class="form-control" value="${response.datos.curso_interes}" id="curso_interes" type="text" placeholder="Ejem. Teologia">
                            </div>
                            </div>
                          </div>
                          </div>
                          </form>
                    `,
                    confirmButtonText: 'Aprobar',
                    showDenyButton: true,
                    showConfirmButton: mostrarAprobar,
                    denyButtonColor: "#DD6B55",
                    denyButtonText: 'Actualizar',
                    showCloseButton: true
                }).then(function(res){
                    if(res.isConfirmed){
                        Swal.fire({
                            icon: 'info',
                            html: `
                            <h2 class="mb-4">Creación de usuario para ${response.datos.nombre}</h2>
                            <div class="row">
                                <div class="col-md-12">
                                <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="usuario" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="usuario" placeholder="Usuario" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="text" class="form-control" id="password" placeholder="******" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Rol</label>
                                        <select class="form-control" id="rol" onchange="cambioRol()" required>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2" id="area-cambio-rol">
                                </div>
                            </div>
                                `,
                                didOpen: function(){
                                    let select_rol = $("#rol")
                                    response.datos.roles.forEach(element => {
                                        select_rol.append(`
                                            <option value="${element.id}">${element.nombre}</div>
                                        `)
                                    });

                                    
                                },
                                preConfirm: function(){
                                    let usuario = document.getElementById('usuario').value;
                                    let pass = document.getElementById('password').value;
                                   
                                    if(usuario.trim() ==''){
                                        Swal.showValidationMessage('Escribe un nombre de usuario')
                                    }else
                                    if(pass.trim() ==''){
                                        Swal.showValidationMessage('Escribe una contraseña')
                                    }
                                },
                                confirmButtonText: 'Registrar usuario',
                                showCloseButton: true
                        }).then(function(r){
                            if(r.isConfirmed){
                                let id_solicitud = response.datos.id
                                let usuario = document.getElementById('usuario').value;
                                let password = document.getElementById('password').value;
                                let rol = document.getElementById('rol').value;
                                let id_grupo;
                                if(rol == 3){
                                    id_grupo = document.getElementById('grupo').value;
                                }else{
                                    id_grupo = 0
                                }
                                $.ajax({
                                    type: "post",
                                    url: "../servidor/usuarios/registrar-nuevo.php",
                                    data: {id_solicitud, usuario, password, rol, id_grupo},
                                    dataType: "json",
                                    success: function (response) {
                                        if(response.estatus){
                                            var icono = 'success';
                                        }else{
                                            var icono = 'error';
                                        }

                                        Swal.fire({
                                            icon: icono,
                                            title: response.mensaje
                                        })
                                        tabla.ajax.reload(null, false)
                                    }
                                });

                            }
                        })
                    }else if(res.isDenied){
                        let formulario = document.getElementById('formulario-solicitante')
                        let formData = new FormData();

                        const inputs = formulario.querySelectorAll('input, select');
                            inputs.forEach((input) => {
                                const id = input.id;
                                const valor = input.value;
                                formData.append(id, valor);
                            });

                            formData.append('id_solicitud', id_solicitud)

                            const apiUrl = '../servidor/solicitudes/actualizar-solicitud.php';
                            try {
                                const respuesta = fetch(apiUrl, {
                                method: 'POST',
                                /* headers: {
                                    'Content-Type': 'application/json',
                                }, */
                                body: formData//datosString,
                                })
                                .then(response => response.text())
                                .then(data => {
                                 let respuesta = JSON.parse(data);
                                  if(respuesta.estatus){
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Actualización existosa',
                                        confirmButtonText: 'Aceptar',
                                    }).then(() =>{
                                        const inputs = document.querySelectorAll('input, select');
                                        inputs.forEach((input) => {
                                            input.value='';
                                        });
                                    })
                                    }
                                });

                            } catch (error) {
                                console.error('Error:', error);
                            }
                    }
                })
            }
        }
    });

    
}

  
function cambioRol(){
    let rol = $("#rol").val();
    if(rol == 3){
        $.ajax({
            type: "post",
            url: "../servidor/grupos/traer-grupos.php",
            data: {'data':'data'},
            dataType: "json",
            success: function (response) {
                if(response.estatus){
                    $("#area-cambio-rol").empty();
                    $("#area-cambio-rol").append(`
                        <label>Asigné un grupo</label>
                        <select class="form-control" id="grupo">
                           
                        </select>`) 
                    response.data.forEach(element => {
                       $("#grupo").append(`
                       <option value="${element.id}">${element.nombre}</option>
                       `)
                    });
                }
            }
        });
    }
                   
}


function actualizarUsuario(id_usuario) {

    id_usuario = parseInt(id_usuario);
    let nombre = document.getElementById("nombre").value;
    let apellido = document.getElementById("apellido").value;
    let proyecto = document.getElementById("proyecto").value;
    let sucursal = document.getElementById("sucursal").value;
    let rol = document.getElementById("rol").value;

    $.ajax({
        type: "POST",
        url: "../servidor/usuarios/actualizar-usuario.php",
        data: {"id": id_usuario, "nombre": nombre, "apellido": apellido, "proyecto_id": proyecto,
               "sucursal_id": sucursal, "rol": rol},
        dataType: "JSON",
        success: function (response) {
            if(response.estatus == true){
                Toast.fire({
                    icon: 'success',
                    title: 'Datos actualizados'
                  })
            }else{
                Toast.fire({
                    icon: 'error',
                    title: response.mensaje
                  })
            }
        }
    });

}




function obtenerParametroGet(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  }

  function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const nacimiento = new Date(fechaNacimiento);

    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();

    // Ajusta la edad si el mes actual es antes del mes de nacimiento o si es el mismo mes pero el día actual es antes
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }

    return edad;
}



const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })