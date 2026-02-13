function corteSwal(){
    Swal.fire({
        icon: "question",
        didOpen: function(){

            $.ajax({
                type: "post",
                url: "../servidor/panel/traer-proyectos.php",
                data: "data",
                dataType: "json",
                success: function (response) {
                    if(response.estatus){
                        $("#proyecto_id").empty();
                        $("#proyecto_id").append('<option value="all">Todos los proyectos</option>');
                        response.datos.forEach(element => {
                            $("#proyecto_id").append(
                                `
                                <option value="${element.id}">${element.nombre}</option>
                                `
                            )
                        });
                        
                    }
                }
            });

            var hoy = new Date();

            $("#fecha-corte").val(hoy.getFullYear() + "-" +  ("0" + (hoy.getMonth() + 1)).slice(-2)  + "-" + hoy.getDate());

            let tipo_corte = $("#tipo-corte");
            
            tipo_corte.change(function (e) { 
                e.preventDefault();

                switch (tipo_corte.val()) {
                    case "normal":
                        $("#area-fechas").empty().append(`
                        <div class="col-12 col-md-6">
                        <label>Fecha</label>
                                <input id="fecha-corte" type="date" class="form-field mt-1">
                        </div>
                        `);
                        break;
                    case "range":
                        $("#area-fechas").empty().append(`
                        <div class="col-12 col-md-6">
                        <label>Fecha incial</label>
                            <input id="fecha-inicial" type="date" class="form-field mt-1">
                        </div>
                        <div class="col-12 col-md-6">
                        <label>Fecha final</label>
                            <input id="fecha-final" type="date" class="form-field mt-1">
                        </div>
                        `);
                        break;
                }
                
            });

        },
        html:`
            <div class="container">
              <div class="row justify-content-center">
                    <div class="col-12 col-md-6">
                        <label>Proyectos</label>
                        <select id="proyecto_id" class="form-field mt-1">
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Tipo de corte</label>
                        <select id="tipo-corte" class="form-field mt-1">
                            <option value="normal">Normal</option>
                            <option value="range">Rango de fechas</option>
                        </select>
                  </div>
              </div>

                  <div class="row mt-3 justify-content-center" id="area-fechas">
                  <div class="col-12 col-md-6">
                        <label>Fecha</label>
                        <input id="fecha-corte" type="date" class="form-field mt-1">
                  </div>
              
              </div>
            
            </div>
        `,
        confirmButtonText: 'Descargar',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        preConfirm: function(){
            if($("#fecha-corte").val() == ""){
                Swal.showValidationMessage(
                    `Selecciona una fecha`
                  )
            }
        }
    }).then((r)=>{
        if(r.isConfirmed){
            realizarCorte();
        }
    })
}


function realizarCorte() {
    let proyecto_id = $('#proyecto_id').val();
    let tipo_corte = $("#tipo-corte").val();
    let sucursal = $("#sucursal").val();
    if(tipo_corte == "normal"){
        let fecha = $("#fecha-corte").val();
        data = { 
            sucursal,
            tipo_corte,
            "fecha": fecha 
        }
        ruta = "../servidor/reportes/realizar-corte.php?fecha=" + fecha + "&tipo_corte=" + tipo_corte  + "&sucursal=" + sucursal + "&proyecto=" + proyecto_id;

    }else{
        let fecha_inicial = $("#fecha-inicial").val();
        let fecha_final = $("#fecha-final").val();
        data = { 
            tipo_corte,
            sucursal,
            "fecha_inicial": fecha_inicial,
            "fecha_final": fecha_final
        }

        ruta = "../servidor/reportes/realizar-corte.php?fecha_inicial=" + fecha_inicial + "&fecha_final=" + fecha_final + "&tipo_corte=" + tipo_corte + "&sucursal=" + sucursal + "&proyecto=" + proyecto_id;
    }

    window.open(ruta, '_blank');
  /*  $.ajax({
    type: "POST",
    url: "../servidor/reportes/realizar-corte.php",
    data: data,
    dataType: "JSON",
    success: function (response) {
        
    }
   }); */
}


