<?php

if(isset($_POST)){

    if(isset($_POST["tipo"])){

        $abono_id = $_POST["abono_id"];
        $existe_dir = file_exists('../../static/docs/C'.$_POST['cliente']);
        if(!$existe_dir){
            mkdir('../../static/docs/C'.$_POST['cliente'], 0777);
        }
        
        move_uploaded_file(
            $_FILES['pdf']['tmp_name'], 
            '../../static/docs/C'. $_POST["cliente"]. '/TICKET-'.$abono_id .'.pdf');
    }else{

        $detalle_id = $_POST["detalle_id"];
        $existe_dir = file_exists('../../static/docs/C'.$_POST['cliente']);
        if(!$existe_dir){
            mkdir('../../static/docs/C'. $_POST["cliente"], 0777);
        }
        move_uploaded_file(
            $_FILES['pdf']['tmp_name'], 
            '../../static/docs/C'. $_POST["cliente"]. '/CONTRATO-'.$detalle_id .'.pdf');
    }

}

?>