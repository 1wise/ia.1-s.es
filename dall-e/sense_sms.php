<?php
    // Dall-e2 form by 1wise.es
    // http://ia.1-s.es
    // http://1wise.es
    //
    // Last edit 05-04-03-2023 00:00
    // Mòdul natiu per integrar la passarel·la API SMS d'Andorra Telecom.
    //
function sense_sms($remMsg, $nomImg, $smsNum, $somApi) {
      global $remMsg, $nomImg, $smsNum, $somApi;
      $emIp = $_SERVER['REMOTE_ADDR'];
      $imgUrl = 'https://iabots.1-s.es/dall-e/imagenes/';
      $smsMsg =  $imgUrl.$nomImg." -".$remMsg;;
      $http_status_som = '';
      $now = date("d/m/Y");
      $validesa = 60;
      $prioritat = 1;
      $somUrl = 'https://sms.andorratelecom.ad/webtosms/sendSms';
      $smsCap = array(
                'Content-Type: application/json',
                'Authorization: Basic ' . $somApi,
                );
       $somMsg = array(
                 'mobils' => $smsNum,
                 'missatge' => $smsMsg,
                 'data' => $now,
                 'validesa' => $validesa,
                 'prioritat' => $prioritat,
                 );
        $somMsgEnc = json_encode($somMsg);
        $smsCurl = curl_init($somUrl);
        curl_setopt($smsCurl, CURLOPT_POST, true);
        curl_setopt($smsCurl, CURLOPT_POSTFIELDS, $somMsgEnc);
        curl_setopt($smsCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($smsCurl, CURLOPT_HTTPHEADER, $smsCap);
        $somRes = curl_exec($smsCurl);
        $http_status = curl_getinfo($smsCurl, CURLINFO_HTTP_CODE); // Get HTTP status code
        $retries = 0;
        while (empty($somRes) && $retries < 5) { // 5 retries limit
           $result = curl_exec($smsCurl);
           if (empty($somRes)) {
               sleep(1); // 1 second delay
               $retries++;
           }
        }
        $status_som = curl_getinfo($smsCurl, CURLINFO_HTTP_CODE);
        curl_close($smsCurl);
        $smsLog  = ">".$emIp."<< - >>".$smsNum."<< - >>".$remMsg."<< - >>".basename($imageFile)."<< - >>".$somRes." - ".date("d-m-Y H:i:s :)").PHP_EOL;
        file_put_contents('LOCOSMS.log', $smsLog, FILE_APPEND);
        if ($status_som === 200) {
           echo "SMS Enviado con Exito !!".$somRes.PHP_EOL;
        } else {
          echo "Fallo envio SMS !!".$somRes.PHP_EOL;
        }
}
?> 
