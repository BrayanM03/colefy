import  Toast  from '../utils/toast.js';

export function tienePermiso(slug) { 
    const permiso = permisos_usuario.find(p => p.slug === slug);

    if (!permiso) {
        return false;
    }

    // Si hay configuración específica para el usuario, tiene prioridad
    if (permiso.valor_usuario !== null) {
        return permiso.valor_usuario == 1;
    }

    // Si no, se toma la del rol
    return permiso.valor_rol == 1;
 }


 export function enlazarLicencia(id_profesor, id_usuario, nombre_usuario) {

    let estado_actual = '';

    if (id_usuario != null) {
        estado_actual = `
            <div class="alert alert-success">
                <h6 class="mb-1">
                    <i class="fa-solid fa-link"></i>
                    Usuario enlazado actualmente
                </h6>

                <div>
                    <b>ID:</b> ${id_usuario}
                </div>

                <div>
                    <b>Usuario:</b> ${nombre_usuario}
                </div>
            </div>
        `;
    } else {
        estado_actual = `
            <div class="alert alert-warning">
                <i class="fa-solid fa-circle-exclamation"></i>
                Este profesor no tiene una licencia enlazada.
            </div>
        `;
    }

    Swal.fire({
        title: 'Licencia del sistema',
        width: 700,
        confirmButtonText: id_usuario != null ? 'Cambiar licencia' : 'Enlazar licencia',
        showDenyButton: id_usuario != null,
        denyButtonText: 'Desenlazar',
        html: `
            <div class="text-start">

                ${estado_actual}

                <hr>

                <label class="form-label">
                    Usuario disponible
                </label>

                <select
                    id="select_usuario_licencia"
                    class="selectpicker form-control"
                    data-live-search="true"
                    data-size="8"
                    title="Selecciona un usuario">

                    <!-- llenar con ajax -->
                </select>

            </div>
        `,
        didOpen: () => {

            //$('.selectpicker').selectpicker();

            // Aquí cargas los usuarios disponibles
            $.ajax({
                type: "post",
                url: BASE_URL + "api/usuarios.php?tipo=combo",
                data: "data",
                dataType: "json",
                success: function (response) {
                   if(response.estatus){
                    response.data.forEach(element => {
                        if(element)
                        $("#select_usuario_licencia").append(`
                        <option value="${element.id}">${element.nombre} ${element.apellido}</option>
                        `)
                    });
                   }
                   
                }
            });
            // traerUsuariosDisponibles();
        }

    }).then((result) => {

        if (result.isConfirmed) {

            let id_usuario_nuevo =
                $('#select_usuario_licencia').val();

            console.log(
                'Enlazar profesor',
                id_profesor,
                'con usuario',
                id_usuario_nuevo
            );

            // ajax enlazar
            $.ajax({ 
                type: "post",
                url: "api/usuarios.php?tipo=enlazar_licencia",
                data: {id_profesor, id_usuario_nuevo, enlazar:true},
                dataType: "json",
                success: function (response) {
                    if(response.estatus){
                        Toast.fire({title: response.mensaje, icon: 'success'})
                    }else{
                        Toast.fire({title: response.mensaje, icon: 'error'})
                    }
                    document.dispatchEvent(new Event('licenciaActualizada'));
                }
            });

        }
        else if (result.isDenied) {

            Swal.fire({
                icon: 'warning',
                title: '¿Desenlazar licencia?',
                text: 'El profesor perderá la asociación con este usuario.',
                showCancelButton: true,
                confirmButtonText: 'Sí, desenlazar'
            }).then((r) => {

                if (r.isConfirmed) {

                    console.log(
                        'Desenlazar profesor',
                        id_profesor
                    );

                    // ajax desenlazar
                    $.ajax({
                        type: "post",
                        url: "api/usuarios.php?tipo=enlazar_licencia",
                        data: {id_profesor, enlazar: false},
                        dataType: "json",
                        success: function (response) {
                            if(response.estatus){
                                Toast.fire({title: response.mensaje, icon: 'success'})
                            }else{
                                Toast.fire({title: response.mensaje, icon: 'error'})
                            }
                            document.dispatchEvent(new Event('licenciaActualizada'));
                        }
                    });
                }

            });

        }

    });

}