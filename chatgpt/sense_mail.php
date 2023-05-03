<?php
require_once '@DIRPHPMAILERSRC/Exception.php';
require_once '@DIRPHPMAILERSRC/PHPMailer.php';
require_once '@DIRPHPMAILERSRC/SMTP.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
	// ChatGPT Form
	// http://ia.1-s.es/
	// http://1wise.es
	//
	// Last edit 01-05-2023 00:00
	//
function sense_mail($emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp) {
      global  $emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp;
      $mail = new PHPMailer();
      $now = date(' d-m-y H:i:s ');
      // SMTP configuration
      try {
        $mail->isSMTP();
        $mail->Host = '@PMSERVERDOMAIN';
//        $mail->SMTPDebug = 2;
//        $mail->Debugoutput = function($str, $level) {
//            file_put_contents('smtp.log', "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
//        };
        $mail->SMTPAuth = true;
        $mail->Username = '@PMUSERDOMAIN';
        $mail->Password = '@PMPASSOWRD';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        // Sender and recipient details
        $mail->setFrom('@PMFROM', $emRem);
//        $mail->addCC('');
        $mail->addBCC('@PMBCC');
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
        $emMsg .= "<p> @SITURL powered by @EMPRESA.</p>";
        $emMsg .= "<p>Consulta a ".$model." via @URLAPP</p>";
        $emMsg .= "<p>Consulta tu log en @URLLOGCRYPT@NOMLOGCRYPT</p>";
        $emMsg .= $miMsg;
        $emMsg .= "</body></html>";
        $mail->Body = $emMsg;

        // Send email
        $mail->send();
 
         $mailLog  = ">".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
        file_put_contents('@NOMGPTMAILLOG', $mailLog, FILE_APPEND); 
        echo "Email enviado !!".PHP_EOL;
 
      } catch (Exception $e) {

        $mailLog  = "ERROR >>>".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
        file_put_contents('@NOMGPTMAILLOG', $mailLog, FILE_APPEND); 
        echo 'Email no se a podido enviar. Error: ' . $mail->ErrorInfo . PHP_EOL;
 
}
}
?>