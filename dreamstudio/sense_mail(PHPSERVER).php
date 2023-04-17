<?php
    // Stability.ai form by 1wise.es
    // http://ia.1-s.es
    // http://1wise.es
    //
    // Last edit 17-04-03-2023 00:00
    //
function sense_mail($emIp, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $dirImg, $imgUrl, $imageNoms, $imageNom, $mMsg, $mHost, $mUsr, $mPas, $mDe, $mBcc) {
      global $emIp, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $dirImg, $imgUrl, $imageNoms, $imageNom, $mMsg, $mHost, $mUsr, $mPas, $mDe, $mBcc;
      $emIp = $_SERVER['REMOTE_ADDR'];
      try {
         $corAle = md5(uniqid(time()));
         $nowForm = date(" d-m-Y h:i:s ;)");
         $emMai = $emUsr;
         $emMsg = $mMsg;
         $emMsg .= $miMsg;
         
         foreach ($imageNoms as $imageNom) {
            $imgNom = $imageNom;
            $emMsg .= '<i><a target="_blank" rel="noreferrer noopener" href="' . $imgUrl . $imgNom . '">'. $imgUrl . $imgNom . '</a></i><br>';
         }
         $emMsg .= "</body></html>";

         // Main header (multipart mandatory)
         $emCap =  "From: " . $emtoAi . " <" . $mDe . ">" . PHP_EOL;
         $emCap .= "Cc: " . PHP_EOL . "Bcc: " . $mBcc  . PHP_EOL;
         $emCap .= "MIME-Version: 1.0" . PHP_EOL;
         $emCap .= "Content-Type: multipart/mixed; boundary=\"" . $corAle . "\"" . PHP_EOL;
         $emCap .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
         $emCap .= "This is a MIME encoded message." . PHP_EOL;

         // Message
         $emCos .= "--" . $corAle . PHP_EOL;
         $emCos .= "Content-Type: text/html; charset=\"utf-8\"" . PHP_EOL;
         $emCos .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
         $emCos .= $emMsg . PHP_EOL . PHP_EOL;

         // Attachment
         foreach ($imageNoms as $imageNom) {
             $imgNom = $imageNom;
             $dirNimg = $dirImg.$imgNom;
             $emCon = file_get_contents($dirNimg);
             $emCon = chunk_split(base64_encode($emCon));
             $emCos .= "--" . $corAle . PHP_EOL;
             $emCos .= "Content-Disposition: attachment; filename=".$imgNom.PHP_EOL;
             $emCos .= "Content-Type: image/png; name=".$imgNom.PHP_EOL;
             $emCos .= "Content-Transfer-Encoding: base64".PHP_EOL;
             $emCos .= "Content-ID: <".$imgNom.">".PHP_EOL;
             $emCos .= $emCon . PHP_EOL;
         } 	

         $emCos .= "--" . $corAle . "--" . PHP_EOL .PHP_EOL;
         mail($emMai, $emAsu, $emCos, $emCap);
   
         $mailLog  = ">".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
         file_put_contents('@NOMSTABILITYMAILLOG', $mailLog, FILE_APPEND); 
         echo " - Email enviado !!".PHP_EOL;
         
      } catch (Exception $e) {
           $exep = "Excepción capturada: ".$e->getMessage()."\n";
           $mailLog  = "ERROR >>>".$exep."<< - >>".$emIp."<< - >>".$emtoAi."<< - >>".$emUsr."<< - >>".$emDat."<< - >>".$remMsg." - ".$nowForm.PHP_EOL;
           file_put_contents('@NOMSTABILITYMAILLOG', $mailLog, FILE_APPEND); 
           echo '- Email no se a podido enviar. Error: ' . $mail->ErrorInfo . PHP_EOL;
      }
}
?>
