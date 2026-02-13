function reporteAbonosGlobal(orden_id){

  
    const doc = new jsPDF();
    let fecha = document.getElementById("fecha-descargar").value
   
    
    fetch('../servidor/abonos/traer-abonos-cliente.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({"orden_id":orden_id, "fecha": fecha})
      })
      .then(response => response.json())
      .then((data) =>{

    //Ajsutando logo dependiendo del proyecto
    if(data.proyecto_id==6){
        logo = data_logo_montealto.replace(/[\s"\\]/gm, "");
      }else if(data.proyecto_id==10){
        logo = data_logo_vallegracia.replace(/[\s"\\]/gm, "");
      }else if(data.proyecto_id==9){
        logo = data_logo_villaanaiz.replace(/[\s"\\]/gm, "");
      }else if(data.datos.id_proyecto==11){
        logo = data_logo_montealto_2.replace(/[\s"\\]/gm, "");
     }else if(data.datos.id_proyecto==12){
       logo = data_logo_magnolia_delsur.replace(/[\s"\\]/gm, "");
     }
    logo_cb = logo_letras_abajo.replace(/[\s"\\]/gm, "");

    logo_whats = logo_whatsapp.replace(/[\s"\\]/gm, "");
    logo_addr = logo_address.replace(/[\s"\\]/gm, "");
  
    doc.addImage(logo_cb, "JPEG", 10, 0, 30, 25);
    doc.addImage(logo, "JPEG", 40, 5, 30, 15);
    doc.setFont("helvetica", "normal"); // set font
    
    doc.setFontSize(10);
    doc.text(fecha,173, 23, 'center');

    doc.addImage(logo_whats, "PNG", 78, 9, 4, 4);
    doc.text(`(868) 274 1147`,95, 12, 'center');
    doc.addImage(logo_addr, "PNG", 76.5, 14, 6, 6);
    doc.text(`C. España 71, Buena Vista, 87497 
Heroica Matamoros, Tamps.`,110, 18, 'center');
  
    doc.setFont("helvetica", "bold"); // set font
    doc.setFontSize(11);
    doc.text("TICKET DE ABONO",162, 12);
    doc.setFontSize(9);
    doc.text("Folio " + data.id + '-' ,170, 16);
  
  
    cliente_mayus = data.cliente_etiqueta.toUpperCase();
/* 
    let pagado = data.pagado
    let saldo = data.saldo */

    doc.text("Nombre del titular: ",10, 35);
    doc.text(cliente_mayus,40, 35);

    DatosGenerales = []
    total_neto = 0;
    if(data.abonos != null){
      penalizacion_sumatoria = 0;
    for (let key in data.abonos) {
        let element1 = data.abonos[key];
        element1.forEach(abono_arreglo => {        
            proyecto = abono_arreglo.proyecto 
            manzana = abono_arreglo.manzana
            lote = abono_arreglo.lote  
            total = abono_arreglo.cantidad
            tipo = abono_arreglo.tipo
            mes = abono_arreglo.mes
            year = abono_arreglo.year
            no_abono = abono_arreglo.no_abono
            penalizacion_monto = parseFloat(abono_arreglo.penalizacion_monto)
            penalizacion_sumatoria += penalizacion_monto

            total_neto += parseFloat(total)
           
            total_form = parseFloat(total).toLocaleString("en");
            total_ = "$" + total_form
            obj_abono = {"manzana": manzana, "lote": lote, "tipo": tipo, "no_abono": no_abono, "mes": mes, "year": year, "total": total_, "penalizacion": penalizacion_monto}
            DatosGenerales.push(obj_abono)
        });    
    };
    
    
  }else{
    obj_abono = {'tipo': 'Sin datos'}
    penalizacion_sumatoria = 0;
    DatosGenerales.push(obj_abono)
    
  }

  var column_header = [
    { header: 'Manzana', dataKey: 'manzana' },
    { header: 'Lote', dataKey: 'lote' },
    { header: 'Tipo', dataKey: 'tipo'},
    { header: 'No. abono', dataKey: 'no_abono'},
    { header: 'Mes', dataKey: 'mes' },
    { header: 'Año', dataKey: 'year' },
    { header: 'Total', dataKey: 'total' },
    { header: 'Penalización', dataKey: 'penalizacion' }
  ]
    //Tabla autotable
    doc.autoTable(({
      headStyles: { fillColor: [211, 211, 211], textColor: [54, 69, 79], halign: 'justify'},
      startY:44,
      body: DatosGenerales,
      styles: { cellPadding: 4, fontSize: 11 },
      margin: { left: 8, top: 8},
      tableWidth: 190,
      columns: column_header,
      didDrawPage: (d) => { 
         alturaPartidas  = d.cursor.y // Calcular altura 
    },
    }))
  
    doc.setFontSize(11)
    penalizacion = penalizacion_sumatoria;//parseFloat(data.penalizacion);
    const formateado_total_neto = total_neto.toLocaleString("en");
    const for_penalizacion = penalizacion.toLocaleString("en");
    neto = parseFloat(total_neto) + parseFloat(penalizacion)
    const neto_form = neto.toLocaleString("en");
    doc.text("Total: $" +  formateado_total_neto ,149, alturaPartidas + 6);
    doc.text("Penalizacion: $" +  for_penalizacion,149,alturaPartidas + 12);
    doc.text("Neto: $" +  neto_form,149,alturaPartidas + 18);
  
  
    var string = doc.output('datauristring');
    var embed = "<embed width='100%' height='100%' src='" + string + "'/>"
    var x = window.open();
    x.document.open();
    x.document.write(embed);
    x.document.close();
    
      })
    
    }
    
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
      }
    
      function cerrar() { 
        window.open('','_parent',''); 
        window.close(); 
     } 
    
     