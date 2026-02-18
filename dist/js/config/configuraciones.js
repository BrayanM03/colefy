import {GeneralEventListener} from '../utils/listeners.js';
 console.log(USER_PERMISSIONS);

$(document).ready(function () {
    const d_escuelas = USER_PERMISSIONS.can_view_escuelas ? '' : 'd-none';
    const d_permisos = USER_PERMISSIONS.can_view_permisos ? '' : 'd-none';
    const d_usuarios = USER_PERMISSIONS.can_view_usuarios ? '' : 'd-none';
    const d_pagos = USER_PERMISSIONS.can_view_pagos ? '' : 'd-none';
    //Cargando listeners
    GeneralEventListener('mostrar-configuraciones', 'click', ventanaConfiguraciones)
    
    function ventanaConfiguraciones(){
        console.log('Configss');
 
        Swal.fire({
            title: 'Configuraciones generales',
            imageUrl: STATIC_URL+'img/icons/cloud.png',   // tu icono personalizado
            imageWidth: 45,
            imageHeight: 45,
            imageAlt: 'Icono',
            html: `
            </div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="list-group">
                            <a href="${BASE_URL}escuelas" class="${d_escuelas} list-group-item list-group-item-action">Escuelas</a>
                            <a href="${BASE_URL}panel_permisos" class="${d_permisos} list-group-item list-group-item-action">Permisos</a>
                            <a href="${BASE_URL}usuarios" class="${d_usuarios} list-group-item list-group-item-action">Usuarios</a>
                            <a href="${BASE_URL}pagos" class="${d_pagos} list-group-item list-group-item-action disabled">Pagos</a>
                        </div>
                    </div>
                </div>
            </div>    
            `,
            showCloseButton: true,
            showCancelButton:false,
            showConfirmButton: false
          })
    }
    
})