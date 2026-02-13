<?php
 
$controller_permiso->validarAcceso(1, CPermiso::VER_ALUMNOS->value);
include "vistas/general/header.php";
 
?>

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
                        <div class="col-12 col-md-9">
                            <h1 class="h3 mb-3">Alumnos</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                        <div style="text-decoration: none; color:white;"> <div id="btn-registrar-alumno" class="btn btn-success" >
                              Registrar alumno</div></div>
                        </div>
                    <!--     <div class="col-12 col-md-3">
                            <label for="proyecto">Grupo</label>
                            <select name="" id="id_grupo" onchange="reloadTable()" class="form-control"> -->
                            <?php
                                                
                                                /*   $select = "SELECT COUNT(*) FROM grupos WHERE estatus = 1";
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
                                                  } */
                                              ?>
                            <!-- </select>
                        </div> -->
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En esta tabla estan los alumnos actuales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <table id="tabla-alumnos" class="table table-hover nowrap" style="width:100%">
                                            <!-- <thead>
                                                <tr>
                                                    <th>Subscriber ID</th>
                                                    <th>Install Location</th>
                                                    <th>Subscriber Name</th>
                                                    <th>some data</th>
                                                </tr>
                                            </thead> -->
                                            </table>
                                        </div>
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
    <script> 
    // Definimos una variable global para JS con la ruta de la API
   /*  const BASE_URL = "<?php //echo BASE_URL; ?>";
    const API_URL = "<?php //echo STATIC_URL; ?>api/"; 
     */
    </script>
    <?php
    include "vistas/general/scripts.php";
    ?>
    
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/alumnos/alumnos.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/utils/listeners.js" type="module"></script>
    <!-- <script src="js/grupos/traer-lista-alumnos.js"></script> -->
  <!--   <script src="js/solicitudes/editar-solicitud.js"></script> -->
   <!-- <script src="js/usuarios/eliminar-usuario.js"></script> -->

</body> 

</html>