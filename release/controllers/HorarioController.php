<?php
require_once __DIR__ . '/../models/Horario.php';
//include  __DIR__ .'/../servidor/helpers/response_helper.php';

class HorarioController
{
    private $id_sesion;

    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
    }
    public function datatable()
    {

        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        // Ordenamiento que manda DataTables
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderColumnName  = $_GET['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

        $horario = new Horario();
        $data = $horario->obtenerHorariosDataTable($start, $length, $search, $orderColumnName, $orderDir);
        $total = $horario->contarHorarios();
        $filtered = $horario->contarHorariosFiltrados($search, $this->id_sesion);


        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $data
        ]);
    }

    public function datatablePreHorario(){
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';
        $horario = new Horario();

        // Ordenamiento que manda DataTables
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderColumnName  = $_GET['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

        $data = $horario->obtenerDetallePreHorarioDataTable($start, $length, $search, $this->id_sesion,  $orderColumnName, $orderDir);
        $total = $horario->contarPreHorarios($this->id_sesion);
        $filtered = $horario->contarPreHorariosFiltrados($search, $this->id_sesion);

        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $data
        ]);
    }

    public function datatableGruposHorario(){
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';
        $horario = new Horario();

        // Ordenamiento que manda DataTables
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderColumnName  = $_GET['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

        $data = $horario->obtenerGruposHorarioDataTable($start, $length, $search, $this->id_sesion,  $orderColumnName, $orderDir);
        $total = $horario->contarGruposHorario($this->id_sesion);
        $filtered = $horario->contarGruposHorarioFiltrados($search, $this->id_sesion);

        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $data
        ]);
    }


    public function agregarDetallePreHorario()
    {
        $id_profesor = $_POST['profesor'];
        $id_materia = $_POST['materia'];
        $dias = $_POST['dia'];
        $hora = $_POST['hora'];

        $horario = new Horario();
        foreach ($dias as $key => $dia) {
            $res =$horario->insertarPreHorario($id_profesor, $id_materia, $dia, $hora, $this->id_sesion);
            if(!$res['estatus']){
                $error = true;
            }else{
                $error = false;
            }
        }; 

        if($error==false){
            echo json_encode(array(
                'estatus'=>true, 'mensaje'=>$res['mensaje'],'tipo'=>'success', 'data'=>[]));
        }else{
            echo json_encode(array(
                'estatus'=>false, 'mensaje'=>$res['mensaje'],'tipo'=>'success', 'data'=>[]
            ));
            
        }
    }

    public function eliminarPreHorario($id_prehorario){
      
        $horario = new Horario();
        $res = $horario->eliminarPreHorario($id_prehorario);
        if($res){
            echo json_encode(array(
                'estatus'=>true, 'mensaje'=>$res['mensaje'], 'tipo'=>'success', 'data'=>$res['data']));
        }
    }

    public function registrarHorario($nombre){
        $horario = new Horario();
        $res = $horario->registrarHorario($nombre, $this->id_sesion);
        $res_reseteo =  $horario->restearDetallePrehorario($this->id_sesion);
        if($res['estatus']){
           echo json_encode(['estatus'=>true, 'mensaje'=>$res['mensaje'],'tipo'=>'success', 'data'=>$res['data'], 'reset'=>$res_reseteo]);
        }else{
            echo json_encode(['estatus'=>false, 'mensaje'=>$res['mensaje'],'tipo'=>'success', 'data'=>$res['data'], 'reset'=>true]);

        }
    }

    public function combo(){
        $horario = new Horario();
        $res = $horario->obtenerListaHorarios();
        return ($res);
    }

    public function insertarGruposHorario($ids_grupos, $id_horario){

        $horario = new Horario();
        foreach($ids_grupos as $key => $value){
            $horario->insertarGruposHorario($value, $id_horario);
        }
        $res = array('estatus'=>true, 'mensaje'=>'Horario asignado correctamente');
        
       echo json_encode($res);
    }

    public function cancelarGruposHorario($id_asignacion){
        $horario = new Horario();
        $res= $horario->cancelarGruposHorario($id_asignacion);
        echo json_encode($res);
    }

}
