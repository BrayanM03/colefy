import { initCustomDataTable } from '../DataTable/datatables-init.js';


let debounceTimer;
let table;
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

$(document).ready(function () {
  const role = $('#role').attr("role");

  const columns = [
    { data: null, title: '#', class:'text-center' },
     { data: 'concepto', title: 'Concepto' },
     { data: 'cantidad', title: 'Cantidad', class:'text-center' },
     { data: 'precio_unitario', title: 'Precio unitario', class:'text-end' },
     { data: 'importe', title: 'Importe', class:'text-end' },
    {
      data: null, title: 'Opciones', class:'text-center',render: function (data, type, row) {
        
          return `
            <div class='row'>
              <div class='col-12 col-md-12 text-center'>
                <div class="btn btn-danger btn-eliminar-concepto">
                  <i class="fa-solid fa-trash"></i>
                </div>
              </div>
            </div>`;
      }
    }
  ];
 
  table = initCustomDataTable('#tabla-conceptos', BASE_URL + 'api/recibos.php?tipo=tabla_conceptos&id_recibo=null&tipo_filtro=nuevo_recibo', columns, [0, 'desc']);
});

$(document).on('change', '#alumno', function() {
  if($(this).val() != ''){
    estilizarBordes('alumno', '', true);
  }
});

$(document).on('change', '#tipo', function() {
  if($(this).val() != ''){
    estilizarBordes('tipo', '', true);
  }
}); 

let ultimaBusqueda='';
$('#alumno').on('shown.bs.select', function () {
  let $input = $('.bs-searchbox input');

  $input.off('input.miEvento').on('input.miEvento', function (e) {
    if (e.keyCode >= 37 && e.keyCode <= 40) return;

    clearTimeout(debounceTimer);

    const value = $(this).val().trim();
    if (!value || value === ultimaBusqueda) return;

    debounceTimer = setTimeout(() => {
      ultimaBusqueda = value;
        buscarAlumno(value);
    }, 400);
});
});


function buscarAlumno(busqueda){
  $.ajax({
    type: "post",
    url: BASE_URL + "api/alumnos.php?tipo=combo",
    data: {busqueda},
    dataType: "json",
    success: function (response) {
      if(response.estatus){
        $("#alumno").empty()
        response.data.forEach(element => {
          $("#alumno").append(`<option value="${element.id}">${element.nombre +' '+ element.apellido_paterno+' '+element.apellido_materno }</option>`)
        });
        $('#alumno').selectpicker('refresh');

      }
    }
  });
}

function setearTipo(e){
  if(e.target.value==1){
    $("#area-plazo").empty()
    $("#label-forma-pago").text('Forma(s) de pago ')
  }else{
    $("#area-plazo").append(`
    <div class="col-12 col-md-4">
        <label for="plazo">Plazo</label>
        <select id="plazo" class="form-control selectpicker">
            <option value="1">7 dias</option>
            <option value="2">15 dias</option>
            <option value="3" selected>1 mes</option>
            <option value="4">45 días</option>
            <option value="5">1 día</option>
            <option value="6">Personalizada</option>
        </select>
    </div>
    `)
    $("#label-forma-pago").text('Forma(s) de pago del adelanto')
    $("#plazo").selectpicker('refresh')

  }
}

function setearFormaPago(){
    let forma_pago = $("#forma-pago").val()
    let area = $("#area-formas-pago")
    $("#suma_total").val('')
    area.empty()
    if(forma_pago.length > 0){
        area.append(`
        <label><b>Montos de la forma de pago</b></label><br>
        <div class="row mt-3" id="lista-formas-pago"></div>`)
        forma_pago.forEach(element => {
            $("#lista-formas-pago").append(`
                <div class="col-12 col-md-6 mb-2">
                    <div class="row mb-1" style="vertical-align:middle !important;">
                        <div class="col-1">
                            <img src="${BASE_URL}static/img/formas_pago/${element}.png" style="width: 30px">
                        </div>
                        <div class="col-10" style="margin-left: 7px; vertical-align:middle !important;">
                            <label>${element}</label>
                        </div>
                    </div>
                    <input class="form-control input-forma-pago" id="f_${element}" type="number" placeholder="0.00">
                </div>
            `)
        });
        estilizarBordes('forma-pago', '', true)

    }else{
        estilizarBordes('forma-pago', 'Selecciona al menos una forma de pago', false)
        area.append(`
        <label><b>Montos de la forma de pago</b></label><br>

        <div class="row mt-3" id="lista-formas-pago">
            <div class="col-12 col-md-12 text-start">
                Sin formas de pago seleccionadas
           </div>
        </div>
        `)
    }
        
}

function setearCategoria(){
    let categoria_recibo = $("#categoria").val();
    if(categoria_recibo){
      estilizarBordes('categoria', '', true)
    }
    let area_categoria = $("#area-categoria")
  
    if(categoria_recibo==1){  //Este ID es de la mensualidad y no podrá cambiar OJO
      area_categoria.append(`
      <div class="col-12 col-md-6">
          <label for="mes">Mes</label>
          <select id="mes" class="form-control selectpicker"
              placeholder="Selecciona una mensualidad"
              data-live-search="true">
              <option value="1">Enero</option>
              <option value="2">Febrero</option>
              <option value="3">Marzo</option>
              <option value="4">Abril</option>
              <option value="5">Mayo</option>
              <option value="6">Junio</option>
              <option value="7">Julio</option>
              <option value="8">Agosto</option>
              <option value="9">Septiembre</option>
              <option value="10">Octubre</option>
              <option value="11">Noviembre</option>
              <option value="12">Diciembre</option>
          </select>
          <small id="small_mes" style="color:tomato;"></small>

      </div>
  
      <div class="col-12 col-md-6">
          <label for="">Año</label>
          <select id="year" class="form-control selectpicker"
              placeholder="Selecciona un año"
              data-live-search="true">
              <option value="2024">2024</option>
              <option value="2025" selected>2025</option>
          </select>
          <small id="small_year" style="color:tomato;"></small>

      </div>
      `);

      $("#mes").selectpicker('refresh')
     
      $("#year").selectpicker('refresh')
    }else{

      area_categoria.empty()
    }

}

$(document).on('change', '#mes', function() {
  if($(this).val() != ''){
    estilizarBordes('mes', '', true);
  }
});

//-----FIN

function setearMonto(e){
  let area = document.getElementById('area-formas-pago');
  let inputs = area.querySelectorAll('input[type="number"]')
  let importe = parseFloat($("#importe_total").val());
  let tipo_recibo = $("#tipo").val()
  let suma =0;
  inputs.forEach(element => {
    let valor = parseFloat(element.value) || 0; // si está vacío, que sea 0
    suma += parseFloat(valor);
  });
  $("#suma_total").val(suma)
  
  if(tipo_recibo==1){
  
    if(suma != importe){
      estilizarBordes('suma_total', 'La suma no coincide con el total.', false)
    }else{
      estilizarBordes('suma_total', '', true)
      estilizarBordes('area-formas-pago', '', true)

    }
  }else{
    if(suma >= 0 && suma <= importe){
      estilizarBordes('suma_total', '', true)
      estilizarBordes('area-formas-pago', '', true)

    }else{
      estilizarBordes('suma_total', 'La suma no puede ser menor a 0 o mayor que el total.', false)
    }
  }
}

function agregarConcepto(){
  let verify = verificarCampos(1)
  if(verify){
    let categoria = $("#categoria").val()
    let cantidad = $("#cantidad").val()
    let precio = $("#precio").val()
    let year =''
    let mensualidad =''
    if(categoria == 1){
      year = $("#year").val()
      mensualidad = $("#mes").val()
    }
    $.ajax({
      type: "post",
      url: BASE_URL + "api/recibos.php?tipo=agregar_concepto",
      data: {cantidad, categoria, precio, mensualidad, year},
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          Toast.fire({icon: "success", title: response.mensaje});
          $("#importe_total").val(response.data['suma'])
        }else{
          Toast.fire({icon: "warning", title: response.mensaje});
        }
        table.ajax.reload(null, false)
      }
    });
  }

}
 
function eliminarConcepto(id){

 
  $.ajax({
    type: "post",
    url: BASE_URL + "api/recibos.php?tipo=eliminar_concepto",
    data: {id},
    dataType: "json",
    success: function (response) {
      if(response.estatus){
        Toast.fire({icon: "success", title: response.mensaje});
        $("#importe_total").val(response.data['suma'])

      }else{
        Toast.fire({icon: "warning", title: response.mensaje});
      }

      table.ajax.reload(null, false)
    }
  });
}

function verificarCampos(tipo){
  
  //Se verificaran los campos al agregar un concepto
  if(tipo==1){
    let categoria = $("#categoria").val()
    let cantidad = $("#cantidad").val()
    let monto = $("#precio").val()

    if(!categoria){
      Toast.fire({icon: "warning", title: 'Selecciona una categoria',});
      estilizarBordes('categoria', 'Selecciona una categoria.', false)
      return false
    } 
    else if(!cantidad){
      Toast.fire({icon: "warning", title: 'Ingresa una cantidad',}); 
      estilizarBordes('cantidad', 'Ingresa una cantidad.', false)
      return false
    } 
    else if(cantidad <= 0){
      Toast.fire({icon: "warning", title: 'La cantidad no puede ser igual o menor que 0',}); 
      estilizarBordes('cantidad', 'La cantidad no puede ser igual o menor que 0', false)
      return false
    } 
    else if(!monto){
      Toast.fire({icon: "warning", title: 'Ingresa un monto',});
      estilizarBordes('precio', 'Ingresa un monto.', false)
      return false
    } 
    else if(cantidad < 0){
      Toast.fire({icon: "warning", title: 'La cantidad no puede ser menor que 0',}); 
      estilizarBordes('precio', 'La cantidad no puede ser menor que 0', false)
      return false
    } 
    
    if(categoria==1){
      let mensualidad = $("#mes").val()
      let year = $("#year").val()
      if(!mensualidad){
        Toast.fire({icon: "warning", title: 'Selecciona una mensualidad',})
        estilizarBordes('mes', 'Selecciona una mensualidad.', false)

        return false;  
      }
      else if(!year){
        Toast.fire({icon: "warning", title: 'Selecciona un año',})
        estilizarBordes('year', 'Selecciona un año.', false)

        return false;  
      };  
    }
    return true;
  }else //Aqui se validaran los campos para generar el recibo
  if(tipo==2){
    let alumno = $("#alumno").val()
    let tipo_recibo = $("#tipo").val()
    let forma_pago = $("#forma-pago").val()
    let sumatoria= $("#suma_total").val()
    let monto= $("#importe_total").val()

    if(!alumno){
      Toast.fire({icon: "warning", title: 'Selecciona un alumno',});
      estilizarBordes('alumno', 'Selecciona un alumno.', false)

      return false
    } else if(!tipo_recibo){
      Toast.fire({icon: "warning", title: 'Selecciona un tipo de recibo',}); 
      estilizarBordes('tipo', 'Selecciona un tipo de recibo.', false)
 
      return false
    }else if(forma_pago.length==0){
      Toast.fire({icon: "warning", title: 'Selecciona al menos una forma de pago',}); 
      estilizarBordes('forma-pago', 'Selecciona una forma de pago.', false)

      return false
    }
    if(forma_pago){
      let flag=true;
      let cadena = '';
      forma_pago.forEach(element => {
        let forma =$("#f_"+element).val()
        if(!forma){
         flag = false;
         if (cadena != '') {
          cadena += ', ';
        }
        cadena += element;
        }
      }); 
      if(!flag){
        Toast.fire({icon: "warning", title: 'Ingresa la forma de pago: ' + cadena}); 
        estilizarBordes('area-formas-pago', 'Ingresa la forma de pago.', false)

        return false
      } 

      if(tipo_recibo ==1){
        monto = parseFloat(monto)
        sumatoria = parseFloat(sumatoria)
        
        if(monto!=sumatoria){
          Toast.fire({icon: "warning", title: 'La suma no coincide con el total ' + cadena});
          estilizarBordes('suma_total', 'La suma no coincide con el total.', false)
          return false
        }
      }

      return true;
    }
  }
}
//$(`#forma-pago`).css('border', '1px solid #59de64').selectpicker('refresh')
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

function comprobacionKeyups(id, e){
  if(id=='cantidad'){
  if(!e.target.value){
    estilizarBordes(id, 'Ingresa una cantidad.', false)
    return true;
  }else if(e.target.value<=0){
    estilizarBordes(id, 'La cantidad no puede ser igual o menor que 0.', false)
    return true;
  }else{
    estilizarBordes(id, '', true)
  }
  }else if(id =='precio'){
    if(!e.target.value){
      estilizarBordes(id, 'Ingresa un monto.', false)
      return true;
    }else if(e.target.value<0){
      estilizarBordes(id, 'El monto no puede menor que 0.', false)
      return true;
    }else{

      estilizarBordes(id, '', true)
    }
  }else{
    estilizarBordes(id, '', true)

  }
  
}

//FUNCIÓN DE PROCESAR RECIBO
function hacerRecibo(){
  const btn_hacer_recibo = document.getElementById('btn-hacer-recibo')
  btn_hacer_recibo.disabled = true;
  btn_hacer_recibo.textContent = 'Realizando...'; // O mostrar un spinner

  let verify = verificarCampos(2);
 if(verify){



  const data = new FormData();

  let alumno = $("#alumno").val()
  let tipo = $("#tipo").val()
  let plazo = !$("#plazo").val() ? 0 : parseInt($("#plazo").val());
  let comentarios = $("#comentarios").val()
  let ciclo = $("#ciclo").val()

  data.append('alumno', alumno)
  data.append('tipo_recibo', tipo)
  data.append('ciclo', ciclo)
  data.append('plazo', plazo)
  data.append('comentarios', comentarios)
  
  let forma_pago = $("#forma-pago").val()
  data.append('formas_pago', forma_pago)

  if(forma_pago.length > 0){
    forma_pago.forEach(element => {
      let forma =$("#f_"+element).val()
      data.append(element, forma)
    });
  }

  $.ajax({
    type: "post",
    processData: false,
    contentType: false,  
    url: BASE_URL + "api/recibos.php?tipo=generar_recibo",
    data: data,
    dataType: "json",
    success: function (response) {
      if(response.estatus){
        window.open('./recibos/pdf/'+response.data.id_recibo,'_blank');

        Swal.fire({
          icon: 'success',
          title: response.mensaje,
          confirmButtonText: 'Entendido'
        })
      }else{
        Swal.fire({
          icon: 'error',
          title: response.mensaje,
          confirmButtonText: 'Entendido'

        })
      }
    }
  }).always(function(){
    btn_hacer_recibo.disabled = false;
    btn_hacer_recibo.textContent = 'Realizar pago'; 
  });
 }else{
  btn_hacer_recibo.disabled = false;
  btn_hacer_recibo.textContent = 'Realizar pago'; 
 }


}


//Listeners 
// --- Continuación de nuevo-recibo.js ---

// Espera a que el documento esté completamente cargado.
document.addEventListener('DOMContentLoaded', () => {
    
  const tipo_select = document.getElementById('tipo');
  const categoria_select = document.getElementById('categoria');
  const forma_pago_select = document.getElementById('forma-pago');
  const formas_pago_area = document.getElementById('area-formas-pago');
  const agregar_forma_pago_btn = document.getElementById('btn-agregar-concepto');
  const tabla_conceptos = $('#tabla-conceptos')
  const btn_hacer_recibo = document.getElementById('btn-hacer-recibo')
  
  if (tipo_select) tipo_select.addEventListener('change', setearTipo);
  if (categoria_select) categoria_select.addEventListener('change', setearCategoria);
  if (forma_pago_select) forma_pago_select.addEventListener('change', setearFormaPago);
  if(agregar_forma_pago_btn) agregar_forma_pago_btn.addEventListener('click', agregarConcepto)
  if(btn_hacer_recibo) btn_hacer_recibo.addEventListener('click', hacerRecibo)
  if (formas_pago_area) {
    // Asigna el listener al elemento padre estático
    formas_pago_area.addEventListener('keyup', (evento) => {
              
              // 🔑 CLAVE: Filtra el evento para asegurarte de que vino de la clase específica
              // Esto comprueba si el elemento que originó el evento tiene la clase 'input-monto-pago'
              if (evento.target.matches('.input-forma-pago')) {
                  // Si sí, llama a tu función de lógica
                  setearMonto(evento);
              }
          });
    }
    
    tabla_conceptos.on('click', '.btn-eliminar-concepto', function (evento) {
        
      // El 'this' es el botón que se hizo clic
      const botonClickeado = $(this);
      
      // 🔑 CLAVE: Usar el API de DataTables para obtener la fila y los datos
      const rowData = table.row(botonClickeado.parents('tr')).data();
      
      if (rowData && rowData.id) {
          // Llamamos a tu función de lógica, que está perfectamente definida en el módulo
          eliminarConcepto(rowData.id);
      } else {
          console.error("No se pudo obtener el ID del concepto.");
      }
  });



  // Usamos document para cubrir todos los elementos dinámicos en la página
  document.addEventListener('keyup', (evento) => {
        
    // El nuevo nombre de la clase genérica para todos los inputs
    const CLASE_IDENTIFICADORA = '.input-verificacion'; 
    
    // 🔑 CLAVE: Verificamos si el elemento que disparó el evento (target)
    // coincide con nuestro selector de inputs dinámicos.
    if (evento.target.matches(CLASE_IDENTIFICADORA)) {
        
        // Si el elemento coincide, llamamos a la función de lógica.
        // Le pasamos el ID y el evento completo, tal como querías.
        // Aunque el ID se puede obtener de evento.target.id,
        // puedes pasarlo si tu función de lógica lo espera:
        
        const idObtenidoDelInput = evento.target.id;
        
        // Llamamos a la función, pasando los "parámetros"
        comprobacionKeyups(idObtenidoDelInput, evento);
    }
});  

});