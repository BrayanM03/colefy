import {GeneralEventListener} from '../utils/listeners.js';
import { subirFoto } from '../utils/images.js';
$(document).ready(function () {
const input = document.getElementById('input-logo');
const id_escuela = input.getAttribute('id_escuela');


GeneralEventListener(
    'input-logo', 
    'change', 
    subirFoto('api/escuelas.php?tipo=actualizar_logo', 'logo', 'img/escuelas', 'escuela', id_escuela)
);

})