
<?php
session_start();

if (empty($_SESSION["id"])) {
    if($_SESSION["rol"] !== "manager"){

        header("Location:not-found.php");
    }
    date_default_timezone_set('America/Matamoros');
}
include "../servidor/database/conexion.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" />

    <title>Asistencia | IIBGV manager</title>

    <link href="css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" />
    
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

            <main class="content">
                <div class="container-fluid p-0">

                    <div class="row mb-2">
                        <div class="col-12 col-md-8">
                            <h1 class="h3 mb-3">Calendario de gestión de asistencia</h1>
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="proyecto">Grupo</label>
                            <select name="" id="grupo" onchange="cargarMaterias()" class="form-control">
                            <?php
                                                
                                                  $select = "SELECT COUNT(*) FROM grupos WHERE estatus = 1";
                                                  $r = $con->prepare($select);
                                                  $r->execute();
                                                  $total = $r->fetchColumn();
                                                  $r->closeCursor();
          
                                                  if($total > 0) {
                                                      $select2 = "SELECT * FROM grupos WHERE estatus = 1";
                                                      $re = $con->prepare($select2);
                                                      $re->execute();
                                                      while($row = $re->fetch()){
                                                         ?>
                                                          <option value="<?php echo $row['id']?>"><?php echo $row['nombre']?></option>
                                                         
                                                         <?php 
                                                      }
                                                      $r->closeCursor();
                                                  }else{
                                                      
                                                      
                                                          ?>
                                                          <option value="null">No hay proyectos registrados</option>
                                                          <?php
                                                  }
                                              ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="fecha-asistencia">Fecha</label>
                            <input type="date" id="fecha-asistencia" class="form-control" today>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Selecciona la materia donde tomaras asistencia</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="materias-area">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <?php
        include "vistas/general/footer.php"
        ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="js/app.js"></script>

    <!-- Librerias -->
    <script src="https://kit.fontawesome.com/5c955c6e98.js" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>

    <script src="js/asistencia/cargar-informacion.js"></script>
    <script>
          document.addEventListener("DOMContentLoaded", function () {
        // Obtener la fecha actual
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Agrega un 0 si el mes es de un dígito
        const day = String(today.getDate()).padStart(2, '0'); // Agrega un 0 si el día es de un dígito

        // Formato 'YYYY-MM-DD' requerido por los inputs de tipo date
        const formattedDate = `${year}-${month}-${day}`;

        // Establecer la fecha actual en el input
        document.getElementById('fecha-asistencia').value = formattedDate;
        cargarMaterias()
    });

    </script>
</body>

</html>