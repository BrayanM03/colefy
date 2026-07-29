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
function cargarMaterias(){
    let id_grupo = $('#grupo').val();
    $("#materias-area").empty();
    $.ajax({
        type: "post",
        url: "../servidor/asistencias/cargar-informacion.php",
        data: {id_grupo},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                response.data.forEach(element => {
                    let hora_materia = convertirHora(element.hora); 
                    $("#materias-area").append(`
                                 <div class="col-12 col-md-4">
                                                <div class="card w-75" style="border:1px solid #939ba2">
                                                    <div class="card-body">
                                                            <h5 class="card-title">${element.nombre}</h5>
                                                            <p class="card-text">${element.codigo} - Hora: ${hora_materia}</p>
                                                            <a href="#" 
                                                            onclick="redirigirPasarAsistencia(${id_grupo}, ${element.id})"
                                                            class="btn btn-primary">Tomar asistencia</a>
                                                    </div>
                                                </div>
                                            </div>
                    `)
                });
            }else{
                $("#materias-area").append(`
                <div class="col-12 col-md-4">
                    <h3>${response.mensaje}</h3>
                </div>
                `);
            }
        }
    });

    console.log(localStorage);
}

function redirigirPasarAsistencia(id_grupo, id_materia){
    let fecha_asistencia = $('#fecha-asistencia').val();
    window.location.href = `pasar-lista-asistencia.php?fecha=${fecha_asistencia}&id_grupo=${id_grupo}&id_materia=${id_materia}`
}

function convertirHora(hora24) {
    const [hora, minuto] = hora24.split(':');
    const horas = parseInt(hora, 10);
    const periodo = horas >= 12 ? 'pm' : 'am';
    const hora12 = horas % 12 || 12; // Convierte 0 y 12 a 12 en formato de 12 horas
    return `${hora12}:${minuto} ${periodo}`;
}

function tomarAsistencia(tipo, id_alumno, id_materia, fecha, id_grupo, deshacer){
    console.log(tipo, id_alumno, id_materia, fecha, id_grupo);
   
    $.ajax({
        type: "post",
        url: "../servidor/asistencias/tomar-asistencia.php",
        data: {tipo, id_alumno, id_materia, fecha, id_grupo, deshacer},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                Toast.fire({
                    icon: "success",
                    title: response.mensaje
                  });

                  setTimeout(function(){
                    window.location.reload();

                  },1000)
            }else{
                Toast.fire({
                    icon: "error",
                    title: response.mensaje
                  });
            }
        }
    });
}
