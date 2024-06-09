<?php
    // Dall-e2 form by 1wise.es
    // http://ia.1-s.es
    // http://1wise.es
    //
    // Last 08-06-2024 00:00    
   //

require_once '@DIRPHPMAILERSRC/PHPMailer/src/Exception.php';
require_once '@DIRPHPMAILERSRC/PHPMailer/src/PHPMailer.php';
require_once '@DIRPHPMAILERSRC/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
function sense_mail($emtoAi, $emUsr, $remMsg, $emAsu, $miMsg, $imageFiles, $nomImg) {
      global $emtoAi, $emUsr, $remMsg, $emAsu, $miMsg, $imageFiles, $nomImg;
      $emIp = $_SERVER['REMOTE_ADDR'];
      $mail = new PHPMailer(true);
      // SMTP configuration
      try {
        $nowForm = date("d-m-Y H:i:s ");
        $mail->isSMTP();
//        $mail->SMTPDebug = 2;
//        $mail->Debugoutput = function($str, $level) {
//            file_put_contents('SMPTPDEBUGLOG.log', "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
//        };
        $mail->Host = '@PMSERVERDOMAIN';
        $mail->SMTPAuth = true;
        $mail->Username = '@PMUSERDOMAIN';
        $mail->Password = '@PMPASSOWRD';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        // Sender and recipient details
        $mail->setFrom('@PMFROM', $emtoAi);
//        $mail->addCC('');
        $mail->addBCC('@PMBCC');
        $recipients = explode(',', $emUsr);
        foreach ($recipients as $recipient) {
            $mail->addAddress(trim($recipient));
        }
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $emAsu;

        $emMsg .= "<html><body>";
        $emMsg .= "<p>Este correo ha sido enviado desde el formulario </p>";
        $emMsg .= "<p> Dall-e3 powered by @EMPRESA </p>";
        $emMsg .= "<p>Consulta a Dall-e @URLSITAPP</p>";
        $emMsg .= "<p>Todo el Archivo de Imagenes @URLIMG</p>";
        $emMsg .= $miMsg;
        $emMsg .= "</body></html>";
        $mail->Body = $emMsg;
        // Attach and embed images
        foreach ($imageFiles as $nomImg) {
            $dirImg = '@DIRIMG';
            $dirNimg = $dirImg.$nomImg;
            $mail->addAttachment($dirNimg, $nomImg);
       }
        // Send email
        $mail->send();

        $mailLog  = ">".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
        file_put_contents('@NOMDALLESMSLOG', $mailLog, FILE_APPEND); 
        echo "Email enviado !!".PHP_EOL;
      } catch (Exception $e) {
        $mailLog  = "ERROR >>>".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
        file_put_contents('@NOMDALLESMSLOG', $mailLog, FILE_APPEND); 
        echo 'Email no se a podido enviar. Error: ' . $mail->ErrorInfo . PHP_EOL;
      }
}
?>
