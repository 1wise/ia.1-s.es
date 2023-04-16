<?php
    // Stability.ai form by 1wise.es
    // http://ia.1-s.es
    // http://1wise.es
    //
    // Last edit 16-04-03-2023 00:00
    //
require_once '@DIRPHPMAILERSRC/Exception.php';
require_once '@DIRPHPMAILERSRC/PHPMailer.php';
require_once '@DIRPHPMAILERSRC/SMTP.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
function sense_mail($emIp, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $dirImg, $imgUrl, $imageNoms, $imageNom, $mMsg, $mHost, $mUsr, $mPas, $mDe, $mBcc) {
    global $emIp, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $dirImg, $imgUrl, $imageNoms, $imageNom, $mMsg, $mHost, $mUsr, $mPas, $mDe, $mBcc;
    ob_start();
    $emIp = $_SERVER['REMOTE_ADDR'];
    $mail = new PHPMailer(true);
    // SMTP configuration
    try {
      $mail->isSMTP();
      $mail->SMTPDebug = 0;
//      $mail->Debugoutput = function($str, $level) {
//            file_put_contents('smtp.log', "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
//        };
      $mail->Host = $mHost;
      $mail->SMTPAuth = true;
      $mail->Username = $mUsr;
      $mail->Password = $mPas;
      $mail->SMTPSecure = 'ssl';
      $mail->Port = 465;
      // Sender and recipient details
      $mail->setFrom($mDe, $emtoAi);
//        $mail->addCC('');
      $mail->addBCC($mBcc);
      $recipients = explode(',', $emUsr);
      foreach ($recipients as $recipient) {
         $mail->addAddress(trim($recipient));
      }
        // Email content
      $mail->isHTML(true);
      $mail->Subject = $emAsu;
      $emMsg = $mMsg;
      $emMsg .= $miMsg;
      foreach ($imageNoms as $imageNom) {
         $imgNom = $imageNom;
         $emMsg .= '<i><a target="_blank" rel="noreferrer noopener" href="' . $imgUrl . $imgNom . '">'. $imgUrl . $imgNom . '</a></i><br>';
      }
      $emMsg .= "</body></html>";
      foreach ($imageNoms as $imageNom) {
         $imgNom = $imageNom;
         $dirNimg = $dirImg.$imgNom;
         $mail->addAttachment($dirNimg, $imgNom);
      }
        // Send email
      $mail->Body = $emMsg;
      $mail->send();
      $mailLog  = ">".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$remMsg." - ".date('d-m-y H:i:s').PHP_EOL;
      file_put_contents('@NOMESTABILITYMAILLOG', $mailLog, FILE_APPEND);
      echo " - Email enviado !!".PHP_EOL;
      ob_clean();
   } catch (Exception $e) {
       $mailLog  = ">>> ERROR >>>".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$remMsg." - ".date('d-m-y H:i:s').PHP_EOL;
       file_put_contents('@NOMESTABILITYMAILLOG', $mailLog, FILE_APPEND);
       echo ' - Email no se a podido enviar. Error: ' . $mail->ErrorInfo . PHP_EOL;
       ob_clean();
   }
}
?>
