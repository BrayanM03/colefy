<?php

  

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    /* require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php'; */
    //Load Composer's autoloader
    require '../../vendor/autoload.php';

    $correo_cliente = $_POST["correo_cliente"];
    $nombre_cliente = $_POST["nombre_cliente"];
    $id_cliente = $_POST["cliente_id"];
    $abono_id = $_POST["abono_id"];
    // Crear una instancia de PHPMailer
    // Crear una nueva instancia de PHPMailer
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;                    //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.cbbienesraices.com.mx';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'ventas@cbbienesraices.com.mx';                     //SMTP username
    $mail->Password   = 'iEus=F+6#rwg';                               //SMTP password
    $mail->SMTPSecure = 'ssl';            //Enable implicit TLS encryption
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('ventas@cbbienesraices.com.mx', 'CBbienes raices');
    $mail->addAddress($correo_cliente, $nombre_cliente);     //Add a recipient
    $mail->addReplyTo('ventas@cbbienesraices.com.mx', 'Information');
   

    //Attachments
    $mail->addAttachment('../../static/docs/C'.$id_cliente.'/TICKET-'. $abono_id .'.pdf'); 
   /*  $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name */

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Ticket de abono FOLIO-' . $abono_id;
    $mail->Body = '
    
            <h3>Ticket de abono</h3><p>
            <div style="color: gray">    
            Estimado comprador, adjuntamos a este correo el ticket de la mensualidad pagada a CBbienes raices.</p>
            <p>Atentamente,</p>
            <p>CBbienes raices</p>
            </div>
            <div class="firma">
                <div style="display: flex; flex-direction:row; justify-content:start; align-items:center;">
                <img src="https://cbbienesraices.com.mx/static/img/logo-cb.png" style="width:30px;"> <b>Bienes Raices</b>
                </div>

                <div style="display: flex; flex-direction:column" style="height: 2rem; color: gray">
                
                    <span>Telefono: +52 8682741147</span>
                    <span>Direccion: Av. Espa帽a 65 entre 12 y 14 Planta Alta Col. Buena Vista Matamoros Tamaulipas</span>
                    <span>Correo: cbernal992@gmail.com</span>
                </div>
            
            </div>';
    
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    $response = array("estatus"=>true, "mensaje"=>'Correo enviado');
} catch (Exception $e) {
    
    $response = array("estatus"=>true, "mensaje"=>"Correo no se ha enviado: {$mail->ErrorInfo}");

}

echo json_encode($response, JSON_UNESCAPED_UNICODE);





?>