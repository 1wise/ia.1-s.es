<?php
require_once './../../PHPMailer/src/Exception.php';
require_once './../../PHPMailer/src/PHPMailer.php';
require_once './../../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
	// ChatGPT Form
	// http://ia.1-s.es/
	// http://1wise.es
	//
	// Last edit 08-04-2023 00:00
	//
function sense_mail($emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp) {
      global  $emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp;
      $mail = new PHPMailer();
      $now = date(' d-m-y H:i:s ');
      // SMTP configuration
      try {
        $mail->isSMTP();
        $mail->Host = 'SMPT.SERVER.COM';
//        $mail->SMTPDebug = 2;
//        $mail->Debugoutput = function($str, $level) {
//            file_put_contents('smtp.log', "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
//        };
        $mail->SMTPAuth = true;
        $mail->Username = 'USER@DOMAIN';
        $mail->Password = 'PASSWORD';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        // Sender and recipient details
        $mail->setFrom('ia.1-s@1wise.es', $emRem);
//        $mail->addCC('');
        $mail->addBCC('henri@sirkia.es');
        $emMai = $emUsr;
        $recipients = explode(',', $emMai);
        foreach ($recipients as $recipient) {
            $mail->addAddress(trim($recipient));
        }
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $emAsu;
        $emMsg .= "<html><body>";
        $emMsg .= "<p>Este correo ha sido enviado desde el formulario </p>";
        $emMsg .= "<p> https://ia.1-s.es powered by https://1wise.es.</p>";
        $emMsg .= "<p>Consulta a ".$model." via https://ia.1-s.es/@/dall-e/</p>";
        $emMsg .= "<p>Todo el Archivo de Imagenes https://im.1-s.es/</p>";
        $emMsg .= $miMsg;
        $emMsg .= "</body></html>";
        $mail->Body = $emMsg;

        // Send email
        $mail->send();
 
         $mailLog  = ">".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
        file_put_contents('LOCOMAIL.log', $mailLog, FILE_APPEND); 
        echo "Email enviado !!".PHP_EOL;
 
      } catch (Exception $e) {

        $mailLog  = "ERROR >>>".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
        file_put_contents('LOCOMAIL.log', $mailLog, FILE_APPEND); 
        echo 'Email no se a podido enviar. Error: ' . $mail->ErrorInfo . PHP_EOL;
 
}
}
?>
