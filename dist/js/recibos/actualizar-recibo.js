import { initCustomDataTable } from "../DataTable/datatables-init.js";
import { formatearFechaEspanol } from '../utils/dates.js';
import {DataTableListener, GeneralEventListener} from '../utils/listeners.js';
import { cancelarPago } from "./pagos.js";

let tabla_conceptos;
let tabla_pagos;
let debounceTimer;

//Definimos la URL de la API a la que queremos hacer la petición
let datos_recibo;
$(document).ready(function () {

  GeneralEventListener('tipo', 'change', setearFechasTipoRecibo)
  GeneralEventListener('btn-actualizar-recibo', 'click', actualizarDatosRecibo)

  const columns_conceptos = [
    { data: null, title: "#" },
    { data: "concepto", title: "Conceptos" },
    { data: "cantidad", title: "Cantidad" },
    { data: "precio_unitario", title: "Precio Unitario" },
    { data: "importe", title: "Importe" },
    {
      data: null,
      title: "Opciones",
      render: function (data) {
       
        return `
              <div class='row'>
                <div class='col-12 col-md-12'>
                  <div class="btn btn-warning" onclick="borrarConcepto(${data.id})">
                    <i class="fa-solid fa-trash"></i>
                  </div>
                </div>
              </div>`;
      },
    },
  ];

  const columns_pagos = [
    { data: null, title: "#" },
    { data: null, title: "Monto", render: (data)=>{
      const monto = formatearCantidad(data['total'])
      let template
      if(data['total'] > 0){
        template = `<b>${monto}</b>`
      }else{
        template = `<span style="color: #828282">${monto}<span>`;
      }
      return template
    }},
    { data:null, title: "Fecha - hora", render: (data)=>{
      const miFecha = new Date(data.fecha);      
      return formatearFechaEspanol(miFecha)
    } },
    { data: null, title: "Efect. 💵", render: (data)=>{
      const monto = formatearCantidad(data['pago_efectivo'])
      let template
      if(data['pago_efectivo'] > 0){
        template = `<b>${monto}</b>`
      }else{
        template = `<span style="color: #828282">${monto}<span>`;
      }
      return template
    } },
    { data: null, title: "Tarj. 💳", render:(data)=>{
      const monto = formatearCantidad(data['pago_tarjeta'])
      let template
      if(data['pago_tarjeta'] > 0){
        template = `<b>${monto}</b>`
      }else{
        template = `<span style="color: #828282">${monto}<span>`;
      }
      return template
    } },
    { data: null, title: "Transf. 📲" , render: (data)=>{
      const monto = formatearCantidad(data['pago_transferencia'])
      let template
      if(data['pago_transferencia'] > 0){
        template = `<b>${monto}</b>`
      }else{
        template = `<span style="color: #828282">${monto}<span>`;
      }
      return template
    }},
    { data: null, title: "Depost. 🏛️", render:(data) =>{
      const monto = formatearCantidad(data['pago_deposito'])
      let template
      if(data['pago_deposito'] > 0){
        template = `<b>${monto}</b>`
      }else{
        template = `<span style="color: #828282">${monto}<span>`;
      }
      return template
    }},
    { data: null, title: "Cheque 📃", render: (data)=>{
      const monto = formatearCantidad(data['pago_cheque'])
      let template
      if(data['pago_cheque'] > 0){
        template = `<b>${monto}</b>`
      }else{
        template = `<span style="color: #828282">${monto}<span>`;
      }
      return template
    }},
    { data: null, title: "Estatus", render:function(data){
      let estatus_tag
      if(data.estatus ==1){
        estatus_tag = '<span class="badge bg-success">Activo</span>'
       }else{
         estatus_tag = '<span class="badge bg-secondary">Cancelado</span>'
       }
       return estatus_tag;
    }},
    { data: null, title: "Tipo", render:function(data){
      let tipo_tag
      if(data.estatus ==2){
        tipo_tag = '<span class="badge bg-success">Liquidación</span>'
       }else{
         tipo_tag = '<span class="badge bg-info">Normal</span>'
       }
       return tipo_tag;
    } },
    { data: "comentarios", title: "Comentarios" },

    {
      data: null,
      title: "Opciones",
      render: function (data) {
        let tipo_cancel = data.estatus ? 1 : 2;
        let btn_fa = data.estatus ? "fa-ban" : "fa-rotate-left";
        let class_btn_edit =  data.estatus ? 'btn-primary' : 'btn-secondary'
        let prop_disabled_btn_edit = data.estatus ? '' : 'disabled'
        let btn_color = data.estatus ? "btn-warning" : "btn-info";
        let alt_text = data.estatus ? "Cancelar pago" : "Descancelar pago";
        return `
             <div class='row'>
               <div class='col-12 col-md-12'>
                 <div class="btn btn-sm" style="background-color:tomato; color:white;" onclick="mostrarReciboPago(${data.id})">
                   <i class="fa-solid fa-file-pdf"></i>
                 </div>
                 <div class="btn ${class_btn_edit} btn-sm ${prop_disabled_btn_edit}" onclick="editarPago(${data.id}, false)">
                    <i class="fa-solid fa-pen-to-square"></i>
                 </div>
                 <div class="btn ${btn_color} btn-sm btn-cancelar-pago" onclick="cancelarPago(${data.id}, ${tipo_cancel})" alt="${alt_text}">
                   <i class="fa-solid ${btn_fa}"></i>
                 </div>
               </div>
             </div>`;
      },
    },
  ];

  tabla_conceptos = initCustomDataTable(
    "#tabla-conceptos-editar",
    BASE_URL + "api/recibos.php?tipo=tabla_conceptos&id_recibo=" +
      id_recibo +
      "&tipo_filtro=editar_recibo",
    columns_conceptos
  );

  tabla_pagos = initCustomDataTable(
    "#tabla-pagos",
    BASE_URL + "api/recibos.php?tipo=tabla_pagos&id_recibo=" + id_recibo,
    columns_pagos
  );
  window.cancelarPago = cancelarPago; 

  //DataTableListener(tabla_pagos, 'click', '.btn-cancelar-pago', cancelarPago)

 
  $("#alumno").on("shown.bs.select", function () {
    let $input = $(".bs-searchbox input"); // Input de búsqueda interno
    $input.off("keyup.miEvento").on("keyup.miEvento", function () {
      clearTimeout(debounceTimer); // Cancelar timeout anterior

      const value = $(this).val().trim();
      if (!value) return; // si no hay texto, no hacemos nada

      debounceTimer = setTimeout(() => {
        // Aquí llamas tu función para hacer la petición AJAX
        buscarAlumno(value);
      }, 400); // Espera 600ms desde la última tecla
    });
  });

  function buscarAlumno(busqueda) {
    $.ajax({
      type: "post",
      url: BASE_URL +"api/alumnos.php?tipo=combo",
      data: { busqueda },
      dataType: "json",
      success: function (response) {
        if (response.estatus) {
          $("#alumno").empty();
          response.data.forEach((element) => {
            $("#alumno").append(
              `<option value="${element.id}">${
                element.nombre +
                " " +
                element.apellido_paterno +
                " " +
                element.apellido_materno
              }</option>`
            );
          });
          $("#alumno").selectpicker("refresh");
        }
      },
    });
  }
 
  obtenerRecibo(id_recibo);
  // Usamos la función fetch para iniciar la petición
  async function obtenerRecibo(id_recibo) {
    const apiUrl = BASE_URL + "api/recibos.php?tipo=obtener_recibo";
    // 1. Los datos que quieres enviar (Payload)
    const datosParaEnviar = {
      id_recibo,
    };

    try {
      const respuesta = await fetch(apiUrl, {
        // 2. Opciones de la petición
        method: "POST", // Especificamos el método HTTP
        headers: {
          // Indicamos que el cuerpo de la petición es JSON
          "Content-Type": "application/json",
        },
        // Convertimos el objeto JavaScript a una cadena JSON para enviarlo
        body: JSON.stringify(datosParaEnviar),
      });

      if (!respuesta.ok) {
        throw new Error(`Error HTTP: ${respuesta.mensaje}`);
      }

      // 3. Obtener la respuesta del servidor (la nueva publicación creada)
      const data_ = await respuesta.json();
      const data = data_["data"];
      let alumno_select = $("#alumno");
      let grupo_select = $("#grupo");
      let tipo_select = $("#tipo");
      let estatus_select = $("#estatus");
      let importe_total_input = $("#importe-total");
      let saldo_pendiente_input = $("#saldo-pendiente");
      let comentario_textarea = $("#comentarios")

      //Seteando montos en pestaña de pagos
      let total_input = $("#total");
      let pagado_input = $("#pagado");
      let restante_input = $("#restante");

      if (data && data.id_alumno) {
        // Asumo que el ID del alumno está en data.alumno_id
        datos_recibo=data;
        const alumnoId = data.id_alumno;
        const alumnoNombreCompleto = data.alumno; // O constrúyelo: data.nombre + ' ' + data.apellido, etc.
        const alumnoGrupo = data.grupo;
        const reciboTipo = data.tipo;
        const reciboEstatus = data.estatus;
        const importeTotal = data.monto_total
        const saldo_pendiente = data.saldo_pendiente
        const pagado = data.monto_total - data.saldo_pendiente
        const comentarios = data.comentario
        if(data.saldo_pendiente==0) document.getElementById('btn-nuevo-pago').classList.add('d-none') //Ocultamos btn nuevo pago en caso de que el recibo ya este pagado
        // --- PASO CLAVE: Agregar la opción al SELECT ---

        // 1. Verificar si la opción ya existe
        if (alumno_select.find(`option[value="${alumnoId}"]`).length === 0) {
          // 2. Si no existe, la agregamos (antes de cualquier otra opción dinámica)
          const newOption = `<option value="${alumnoId}">${alumnoNombreCompleto}</option>`;
          alumno_select.append(newOption);
     
        }

     
       
        // --- PASO CLAVE: Seleccionar y Refrescar ---

        // 3. Establecer el valor del SELECT al ID del alumno del recibo
        alumno_select.val(alumnoId);
        grupo_select.val(alumnoGrupo);
        tipo_select.val(reciboTipo);
        estatus_select.val(reciboEstatus);
        importe_total_input.val(importeTotal)
        saldo_pendiente_input.val(saldo_pendiente)
        
        total_input.val(importeTotal)
        pagado_input.val(parseFloat(pagado))
        restante_input.val(saldo_pendiente)
        comentario_textarea.val(comentarios)

        // 4. Refrescar el selectpicker para actualizar la vista
        alumno_select.selectpicker("refresh");
        tipo_select.selectpicker("refresh");
        estatus_select.selectpicker("refresh");

        //SETEAR plazo

        setearFechasTipoRecibo()

      
      }
    } catch (error) {
      console.error("❌ Hubo un problema al enviar los datos:", error.message);
    }
  }

  function setearFechasTipoRecibo(){
    let tipo_recibo = document.getElementById('tipo').value
    let area_fechas = $("#area-fechas")

    if(tipo_recibo==2){
      area_fechas.empty().append(`
        <div class="col-12 col-md-3">
            <label for="">Plazo</label>
            <select name="" id="plazo"
                class="form-control selectpicker"
                data-live-search="true">
                <option value="">Seleccione un plazo</option>
                <option value="1">7 dias</option>
                <option value="2">15 dias</option>
                <option value="3">1 mes</option>
                <option value="4">45 días</option>
                <option value="5">1 día</option>
                <option value="7">Personalizado</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label for="">Fecha</label>
            <input class="form-control" type="datetime-local" id="fecha">
        </div>
        <div class="col-12 col-md-4">
            <label for="">Fecha de vencimiento</label>
            <input class="form-control" type="datetime-local" id="fecha-vencimiento" disabled>
        </div>
      `)
      let plazo = $("#plazo")
      plazo.val(datos_recibo.plazo)
      plazo.selectpicker('refresh')

      //Setando fechas
      let fecha_input = $("#fecha")
      const d = new Date(datos_recibo.fecha_registro);// 1. Convertir a objeto Date
      const formato = d.toISOString().slice(0,16);// 2. Formatear a yyyy-mm-ddThh:mm
      fecha_input.val(formato)

      let fecha_venc_input = $("#fecha-vencimiento")
      const d_v = new Date( datos_recibo.fecha_vencimiento);// 1. Convertir a objeto Date
      const formato_v= d_v.toISOString().slice(0,16);// 2. Formatear a yyyy-mm-ddThh:mm
      fecha_venc_input.val(formato_v)

      GeneralEventListener('plazo', 'change', cambioPlazo)


    }else if(tipo_recibo==1){
      area_fechas.empty().append(`
          <div class="col-12 col-md-3">
              <label for="">Fecha</label>
              <input class="form-control" type="datetime-local" id="fecha">
          </div>
      `)
      let fecha_input = $("#fecha")
      const d = new Date( datos_recibo.fecha_registro);// 1. Convertir a objeto Date
      const formato = d.toISOString().slice(0,16);// 2. Formatear a yyyy-mm-ddThh:mm
      fecha_input.val(formato)
    }

  }

  // Función para forzar el ajuste en todas las DataTables
  function ajustarDataTables() {
    // Itera sobre todas las tablas inicializadas en la página
    $.fn.dataTable.tables(true).forEach(function (table) {
      var dt = $(table).DataTable();

      // El método más importante es columns.adjust()
      dt.columns.adjust();

      // Si usas la extensión Responsive, este paso es crucial
      if (dt.responsive) {
        dt.responsive.recalc();
      }
    });
  }

  // Escuchar el evento que se dispara cuando cambia el hash de la URL
  window.addEventListener("hashchange", function () {
    // Aumenta el retraso.
    // 300ms es un buen punto de partida para dar tiempo a que el navegador
    // termine de renderizar el contenido visible y las animaciones CSS.
    setTimeout(ajustarDataTables, 50);
  });

  // También es buena idea ajustarlas ligeramente al cargar la página
  window.addEventListener("load", function () {
    setTimeout(ajustarDataTables, 50);
  });
});

function cambioPlazo(){
  const plazo_select = $("#plazo")

  if(plazo_select.val()==7){
    $("#fecha-vencimiento").prop('disabled', false)
  }else{
    $("#fecha-vencimiento").prop('disabled', true)

    const fechaBaseStr = $("#fecha").val(); // formato: yyyy-mm-ddThh:mm
    if (!fechaBaseStr) return;
     // Convertir a Date respetando hora local
     const d = new Date(fechaBaseStr);
     switch (plazo_select.val()) {
         case "1": // 7 días
             d.setDate(d.getDate() + 7);
             break;
 
         case "2": // 15 días
             d.setDate(d.getDate() + 15);
             break;
 
         case "3": // 1 mes
             d.setMonth(d.getMonth() + 1);
             break;
 
         case "4": // 45 días
             d.setDate(d.getDate() + 45);
             break;
 
         case "5": // 1 día
             d.setDate(d.getDate() + 1);
             break;
 
         case "7": 
             // Personalizado → tú decides qué hacer, por ejemplo abrir modal
             console.log("Plazo personalizado seleccionado");
             return; 
     }
 
     // Formatear a yyyy-mm-ddThh:mm
     const yyyy = d.getFullYear();
     const mm = String(d.getMonth() + 1).padStart(2, '0');
     const dd = String(d.getDate()).padStart(2, '0');
     const hh = String(d.getHours()).padStart(2, '0');
     const mi = String(d.getMinutes()).padStart(2, '0');
 
     const formatted = `${yyyy}-${mm}-${dd}T${hh}:${mi}`;
 
     $("#fecha-vencimiento").val(formatted);
  }

}

function formatearCantidad(cantidad) {
  // Si la cantidad es 0, 0.00, null, undefined o false, retorna un guion
  if (!cantidad || cantidad === 0 || cantidad === '0') {
    return '-';
  }
  
  // Convertir a número por si viene como string
  const num = parseFloat(cantidad);
  
  // Verificar si es un número válido
  if (isNaN(num) /* || num == 0 */) {
    return '-';
  }
  
  // Formatear como moneda USD con separadores de miles y 2 decimales
  return num.toLocaleString('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

export function retornarRecibo(){
  return datos_recibo;
}

export function estilizarBordes(id, mensaje, estatus){
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


//ACTUALIZAR DATOS GENERALES DEL RECIBO

function actualizarDatosRecibo(){
  const contenedor = document.getElementById('contenedor-ids-recibo');
  const id_recibo = contenedor.dataset.idRecibo;
  let alumno = document.getElementById('alumno').value
  let tipo = document.getElementById('tipo').value
  let fecha = document.getElementById('fecha').value
  let estatus = document.getElementById('estatus').value
  let comentarios = document.getElementById('comentarios').value
  let fecha_vencimiento
  if(tipo==2){
    fecha_vencimiento = document.getElementById('fecha-vencimiento').value
  }else{
    fecha_vencimiento = null;
  }


  $.ajax({
    type: "post",
    url: BASE_URL+'api/recibos.php?tipo=actualizar_datos_generales',
    data: {data:{id_recibo, id_alumno:alumno, tipo, fecha, estatus, comentarios, fecha_vencimiento}},
    dataType: "json",
    success: function (response) {
      const icon_resp = response.estatus ? 'success': 'error';

      Swal.fire({
        title: response.mensaje,
        icon: icon_resp,
        confirmButtonText: 'Entendido',
        showCloseButton: true
    })
    }
  });

}

export {tabla_pagos}