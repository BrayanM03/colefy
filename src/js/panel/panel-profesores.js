

function setearTablaGrupo(id_grupo){
    let area = $("#area-grupo")
    let id_ciclo = $("#ciclo").attr('id_ciclo');
    
    area.empty();
    area.append(`
        <div class="row">
            <div class="col-12 text-center">
            <img src="${BASE_URL}/static/img/loading.gif" style="width:30px;"></img>
            </div>
        </div>
    `)

    setTimeout(()=>{
        area.empty();
        area.append(`
        <table class="table table-hover">
            <thead>
                <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>1er periodo</th>
                <th>2do periodo</th>
                <th>3er periodo</th>
                </tr>
            </thead>
            <tbody id="tbody-alumnos"></tbody>
        </table>
    `);

    $.ajax({
        type: "post",
        url: BASE_URL + "api/grupos.php?tipo=grupo_calificaciones",
        data: {id_grupo, id_ciclo},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                response.data.forEach(element => {
                    console.log(element.nombre);
                    $("#tbody-alumnos").append(`
                    <tr>
                        <td>${element.id}</td>
                        <td>${element.nombre} ${element.apellido_paterno} ${element.apellido_materno}</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    `)
                    
                });
            }
        }
    });
    }, 700)
    


}