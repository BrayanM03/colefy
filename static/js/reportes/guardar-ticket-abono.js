function guardarTicketAbono(abono_id){ 
    // Landscape export, 2×4 inches
    const doc = new jsPDF();
      
    
    fetch('../servidor/abonos/traer-ticket-abono.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({"abono_id":abono_id})
      })
      .then(response => response.json())
      .then((data) =>{
    
 //Ajsutando logo dependiendo del proyecto
 if(data.datos.id_proyecto==6){
  logo = data_logo_montealto.replace(/[\s"\\]/gm, "");
}else if(data.datos.id_proyecto==10){
  logo = data_logo_vallegracia.replace(/[\s"\\]/gm, "");
}else if(data.datos.id_proyecto==9){
  logo = data_logo_villaanaiz.replace(/[\s"\\]/gm, "");
}else if(data.datos.id_proyecto==11){
  logo = data_logo_montealto_2.replace(/[\s"\\]/gm, "");
}else if(data.datos.id_proyecto==12){
logo = data_logo_magnolia_delsur.replace(/[\s"\\]/gm, "");
}else if(data.datos.id_proyecto==13){
logo = data_logo_bosques_primavera.replace(/[\s"\\]/gm, "");
}
        logo_cb = logo_letras_abajo.replace(/[\s"\\]/gm, "");
    
        logo_whats = logo_whatsapp.replace(/[\s"\\]/gm, "");
        logo_addr = logo_address.replace(/[\s"\\]/gm, "");
      
        doc.addImage(logo_cb, "JPEG", 10, 0, 30, 25);
        doc.addImage(logo, "JPEG", 40, 5, 30, 15);
        doc.setFont("helvetica", "normal"); // set font
        
        doc.setFontSize(10);
        doc.text(data.datos.datos_abono.fecha,173, 23, 'center');
    
        doc.addImage(logo_whats, "PNG", 78, 9, 4, 4);
        doc.text(`(868) 274 1147`,95, 12, 'center');
        doc.addImage(logo_addr, "PNG", 76.5, 14, 6, 6);
        doc.text(`C. España 71, Buena Vista, 87497 
    Heroica Matamoros, Tamps.`,110, 18, 'center');
      
        doc.setFont("helvetica", "bold"); // set font
        doc.setFontSize(11);
        doc.text("TICKET DE ABONO INDIVIDUAL",142, 12);
        doc.setFontSize(9);
        doc.text("Folio " +data.datos.datos_abono.id,170, 16);
  
        cliente_mayus =  data.datos.cliente.toUpperCase();
  
      doc.text("Nombre del titular: ",10, 35);
      doc.text(cliente_mayus,40, 35);
  
  
   /*  doc.setFont("helvetica", "bold"); // set font
    doc.setFontSize(11);
    doc.text( "TICKET DE ABONO INDIVIDUAL",82, 17);
    doc.setFontSize(9);
    doc.text("Folio " +  data.datos.datos_abono.id,89, 33); */
  
    pagado = data.datos.pagado
    saldo = data.datos.saldo           
    proyecto = data.datos.proyecto 
    manzana = data.datos.manzana
    lote = data.datos.lote  
    fecha = data.datos.datos_abono.fecha
    mes = data.datos.datos_abono.mes
    year = data.datos.datos_abono.year
    total = data.datos.datos_abono.total
    tipo = data.datos.datos_abono.tipo
    no_abono = data.datos.datos_abono.no_abono
   
    let penalizacion = data.datos.datos_abono.penalizacion_monto;
  
    DatosGenerales = [{"manzana": manzana,"lote": lote, "tipo": tipo, "penalizacion": penalizacion,  "no_abono": no_abono, "mes": mes, "year": year, "total": total}]
    bodyContract = [{data: DatosGenerales}]
    //Tabla autotable
    doc.autoTable(({
      headStyles: { fillColor: [211, 211, 211], textColor: [54, 69, 79], halign: 'justify'},
      startY:44,
      body: DatosGenerales,
      styles: { cellPadding: 4, fontSize: 11 },
      margin: { left: 8, top: 8},
      tableWidth: 190,
      columns: [
        { header: 'Manzana', dataKey: 'manzana' },
        { header: 'Lote', dataKey: 'lote' },
        { header: 'Tipo', dataKey: 'tipo'},
        { header: 'No. abono', dataKey: 'no_abono'},
        { header: 'Mes', dataKey: 'mes' },
        { header: 'Año', dataKey: 'year' },
        { header: 'Total', dataKey: 'total' },
        { header: 'Penalizacion', dataKey: 'penalizacion' },
      ],
    }))
  
    doc.setFontSize(11)
    /* doc.text("Precio total $" +  data.datos.precio,129, 70); */
   /*  doc.text("Pagado $" +  data.datos.pagado,129, 76);
    doc.text("Saldo pendiente $" +  data.datos.saldo,129, 82); */
  
    /* penalizacion = parseFloat(data.datos.penalizacion); */
  
    total = parseFloat(total);
    const formateado_total_neto = total.toLocaleString("en");
 
    const for_penalizacion = penalizacion.toLocaleString("en");
    neto = parseFloat(total) + parseFloat(penalizacion)
    const neto_form = neto.toLocaleString("en");
    doc.text("Total: $" +  formateado_total_neto ,129, 82);
    doc.text("Penalizacion: $" +  for_penalizacion,129,88);
    doc.text("Neto $" +  neto_form,129, 94);
  
  
   /*  doc.text("Proyecto " +  data.datos.proyecto,70, 70);
    doc.text("Manzana " +  data.datos.manzana,70, 76);
    doc.text("Lote " +  data.datos.lote,70, 82);
    doc.addImage(logo, "JPEG", 75, 90, 50, 23); */
  
  
    //----------COPIA----------//
  
  
    doc.addImage(logo_cb, "JPEG", 10, 127, 30, 25);
    doc.addImage(logo, "JPEG", 40, 132, 30, 15);
    doc.setFont("helvetica", "normal"); // set font
    
    doc.setFontSize(10);
    doc.text(data.datos.datos_abono.fecha,173, 150, 'center');
  
    doc.addImage(logo_whats, "PNG", 78, 136, 4, 4);
    doc.text(`(868) 274 1147`,95, 139, 'center');
    doc.addImage(logo_addr, "PNG", 76.5, 141, 6, 6);
    doc.text(`C. España 71, Buena Vista, 87497 
  Heroica Matamoros, Tamps.`,110, 145, 'center');
  
    doc.setFont("helvetica", "bold"); // set font
    doc.setFontSize(11);
    doc.text("TICKET DE ABONO INDIVIDUAL",142, 139);
    doc.setFontSize(9);
    doc.text("Folio " +data.datos.datos_abono.id,170, 143);
  
  doc.text("Nombre del titular: ",10, 162);
  doc.text(cliente_mayus,40, 162);
  
     //Tabla autotable
     doc.autoTable(({
      headStyles: { fillColor: [211, 211, 211], textColor: [54, 69, 79], halign: 'justify'},
      startY:171,
      body: DatosGenerales,
      styles: { cellPadding: 4, fontSize: 11 },
      margin: { left: 8, top: 8},
      tableWidth: 190,
      columns: [
        { header: 'Manzana', dataKey: 'manzana' },
        { header: 'Lote', dataKey: 'lote' },
        { header: 'Tipo', dataKey: 'tipo'},
        { header: 'No. abono', dataKey: 'no_abono'},
        { header: 'Mes', dataKey: 'mes' },
        { header: 'Año', dataKey: 'year' },
        { header: 'Total', dataKey: 'total' },
        { header: 'Penalizacion', dataKey: 'penalizacion' },
      ],
    }))
  
    doc.setFontSize(11)
  
    doc.text("Total: $" +  formateado_total_neto ,129, 209);
    doc.text("Penalizacion: $" +  for_penalizacion,129,215);
    doc.text("Neto $" +  neto_form,129, 220);
  
  
    /* var string = doc.output('datauristring');
    var embed = "<embed width='100%' height='100%' src='" + string + "'/>"
  var x = window.open();
  x.document.open();
  x.document.write(embed);
  x.document.close(); */

  var blob = doc.output('blob');
    var formData = new FormData();
    formData.append('pdf', blob);
    formData.append('cliente', data.datos.id_cliente);
    formData.append('abono_id', abono_id);
    formData.append('tipo', 'ticket');
    
    $.ajax({
      url: '../servidor/reportes/guardar-contrato-servidor.php',
     
      method: 'POST',
      data: formData,
      processData: false,
                    contentType: false,
      success: function(response) {
        console.log(response)
        console.log('Archivo guardado en el servidor');

        $.ajax({
            type: "POST",
            url: "../servidor/correo/enviar-correo-ticket.php",
            data: {"nombre_cliente": data.datos.cliente, 
            "correo_cliente": data.datos.correo, "cliente_id": data.datos.id_cliente,
             'abono_id': abono_id},
            dataType: "JSON",
            success: function (response) {
                if(response.estatus == false){
                    $("#estatus-correo").text(response.mensaje)
                }
            }
        });

      },
      error: function(error) {
        console.error('Error al guardar el archivo en el servidor');
      }
    });
    
      })
           
    
    
    
    }
    
    /* 
    console.log(doc.getFontList()); */
    
    
    
    
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