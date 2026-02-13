<!DOCTYPE html>
<html lang="en">
<?php
session_start();

if (empty($_SESSION["id"] || $_SESSION['rol'] !== 'manager')) {
    header("Location:login.php");
} ?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/pages-sign-up.html" />

	<title>Nuevo documento | ERP manager</title>

	<link href="css/app.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
	<style>
		#btn-user, #btn-password{
		  width: 97px;
		  height: 32.59px;
		  display: flex;
		  justify-content: center;

		}
		.preloader {
  width: 16px;
  height: 16px;
  border: 2.5px solid #eee;
  border-top: 2.5px solid tomato;
  border-radius: 50%;
  animation-name: girar;
  animation-duration: 2s;
  animation-iteration-count: infinite;
}
@keyframes girar {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
	</style>
</head>

<body>
<div class="wrapper">

<?php
include "vistas/general/sidebar.php"
?>
<div class="main">
<?php
include "vistas/general/navbar.php"
?>

	<main class="d-flex w-100 mt-4">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
					<div class="d-table-cell">

						<div class="text-center mt-4">
							<h1 class="h2">Nuevo registro de documentos</h1>
							<p class="lead">
								Desde este panel puedes crear un registro de documento, solo arrastra y suelta los documentos que subiras y colocale un nombre y descripción al registro.
							</p>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-4">
                                    <form id="form-docs">
                                            <div class="mb-3">
                                                <label class="form-label">titulo</label>
                                                <input required class="form-control form-control-lg animate__animated" type="text" id="titulo" name="titulo" placeholder="Ingresa un titulo" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descripción</label>
                                                <textarea class="form-control form-control-lg animate__animated" id="descripcion" name="descripcion" placeholder="Ingresa una descripción"></textarea>
                                            </div>
                                            <label class="form-label">Documentos:</label>
                                    <div class="dropzone" id="my-awesome-dropzone">
                                         <div class="dz-message" data-dz-message><span>Suelta tus documentos aquí</span></div>
                                    </div>
                                    <div class="text-center mt-3">
									      	<a href="documentos-subidos.php" class="btn btn-lg btn-secondary">Volver</a>

											<a class="btn btn-lg btn-primary" id="submitBtn">Registrar</a>
											<!-- <button type="submit" class="btn btn-lg btn-primary">Sign up</button> -->
										</div>
                                        </form>
                                       
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>
	</main>
</div>

	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="js/app.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
	
    <script>
  // Note that the name "myDropzone" is the camelized
  // id of the form.
  let myDropzone = new Dropzone("#my-awesome-dropzone", { 
    addRemoveLinks: true,
    url: "#",
    autoProcessQueue: false ,
    removedfile: function(file) {
            file.previewElement.remove();
    }

    });
   // myDropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar elemento";
    myDropzone.on('addedfile', function(file) {

        var ext = file.name.split('.').pop();

        if (ext == "pdf") {
            $(file.previewElement).find(".dz-image img").attr("src", "./img/pdf.png").css('width', '130px');
        } else if (ext.indexOf("doc") != -1) {
            $(file.previewElement).find(".dz-image img").attr("src", "./img/word.png").css('width', '130px');
        } else if (ext.indexOf("xls") != -1) {
            $(file.previewElement).find(".dz-image img").attr("src", "./img/excel.png").css('width', '130px');
        }
});
 
// Manejar el clic en el botón de envío
document.getElementById("submitBtn").addEventListener("click", function() {
  // Verificar si hay archivos en la cola
   title = document.getElementById("titulo").value;

  if (myDropzone.getQueuedFiles().length > 0 && title.trim() != '') {
    // Procesar los archivos subidos
    myDropzone.processQueue();
    registrar()
  } else {
    if(myDropzone.getQueuedFiles().length == 0){
        // Si no hay archivos, enviar el formulario directamente
        Swal.fire({
                icon:'error',
                title: 'No hay archivos subidos'
            })
    }

    if(title.trim() == ''){
        Swal.fire({
                icon:'error',
                title: 'Establece un titulo'
            })
    }
    
  }
});




function registrar() {
  // Crear un objeto FormData para almacenar los valores
  let formData = new FormData();

  // Obtener el formulario
  const form = document.getElementById('form-docs');

  // Recorrer todos los inputs y textareas
  form.querySelectorAll('input, textarea').forEach((input, textarea) => {
    // Agregar cada valor de input o textarea al FormData
    formData.append(input.name, input.value);
  });

  // Obtener archivos de Dropzone (si estás usando Dropzone.js)
  const dropzone = Dropzone.forElement("#my-awesome-dropzone");
  
  // Agregar cada archivo de Dropzone al FormData
  dropzone.files.forEach((file, index) => {
    formData.append('file_' + index, file);
  });
  // Enviar datos al servidor usando fetch
  fetch('../servidor/documentos/subir-registro.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    Swal.fire({
        icon:'success',
        title: result.message
    })
  })
  .catch(error => {
    console.error('Error:', error);
  });
}

</script>

</body>

</html>