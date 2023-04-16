<?php
global $emtoAi, $emUsr, $remMsg, $emAsu, $miMsg, $imageFiles, $dirNimg, $nomImg, $smsNum, $somApi;
require_once './sense_mail.php';
require_once './sense_sms.php';
    // Dall-e2 form by 1wise.es
    // http://ia.1-s.es
    // http://1wise.es
    //
    // Last edit 05-04-03-2023 00:00
    //
  function wrap_text($text, $max_width = 80) {
    return wordwrap($text, $max_width, "\n", true);
}

function add_caption_to_png_metadata($file_path, $caption) {
    // Wrap the caption to ensure lines are no longer than 80 characters
    $wrapped_caption = wrap_text($caption);

    // Create an Imagick object
    $img = new Imagick($file_path);

    // Set the caption as metadata
    $img->setImageProperty('Caption', $wrapped_caption);

    // Save the image with the new metadata
    $img->writeImage($dirNimg);
}

    $emIp = $_SERVER['REMOTE_ADDR'];
    $somUsu = '';
    $somPas = '';
    $somApi = base64_encode("$somUsu:$somPas");
    $emDat = $_POST['prompt'];
    $nowForm = date("d-m-Y H:i:s ");
    $prompt = $_POST['prompt'];
    $size = $_POST['size'];
    $numimg = intval($_POST['numimg']);
    $emRem = $_POST['emrem'];
    if (strpos($emRem, "-") !== false) {
      $emtoAi = strstr($emRem, '-', true);
    } else {
         $emtoAi = $emRem;
    }
    $emUsr = '';
    if (preg_match('~_m_(.*?)_m_~', $emRem, $emUsch)) {
       $emUsr = $emUsch[1];
    }
    $smsNum = '';
    if (preg_match('~_conM_(.*?)_Isms_~', $emRem, $smsNch)) {
        $smsNum = $smsNch[1];
    }
    $remMsg = '';
    if (preg_match('~_mSg_(.*?)_mSg_~', $emRem, $mstch)) {
        $remMsg = $mstch[1];
    }
    $emTai = $emtoAi." con Dall-e: ".$emDat;
    $model = $_POST['model'];
    $apiKey = $_POST['api-key'];
    $imgUrl = 'https://iabots.1-s.es/dall-e/imagenes/';
    $apiUrl = 'https://api.openai.com/v1/images/generations';
    $emIp = $_SERVER['REMOTE_ADDR'];
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] == true) {
    $imageFiles = [];
    $headers = array(
      'Authorization: Bearer ' . $apiKey,
      'Content-Type: application/json',
    );
    $data = array(
      'model' => $model,
      'prompt' => $prompt,
      'size' => $size,
      'n' => $numimg,
      'user' => $emtoAi,
    );

    $aiCurl = curl_init();
    curl_setopt($aiCurl, CURLOPT_URL, $apiUrl);
    curl_setopt($aiCurl, CURLOPT_POST, 1);
    curl_setopt($aiCurl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($aiCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($aiCurl, CURLOPT_HTTPHEADER, $headers);

    $aiRES = curl_exec($aiCurl);
    $httpcode = curl_getinfo($aiCurl, CURLINFO_HTTP_CODE);
    curl_close($aiCurl);

   if ($httpcode == 200) {
      $now = date(' d-m-y H:m:s ');
      $emIp = $_SERVER['REMOTE_ADDR'];
      $result = json_decode($aiRES, true);
      $images = $result['data'];
      $texto = "<br><b>".$prompt." ".date("y-m-d H:i:s")."</b><br>";
      $chunk_size = 30;
      $texto = chunk_split($texto, $chunk_size, "\r\n"); 
      $imageFiles = array();
      $imageFiles_str = '';
      $imageFiles_pre = '';
      foreach ($images as $image) {
         $imageUrl = $image['url'];
         $imageData = file_get_contents($imageUrl);
         $nowForm = microtime(true);
         $milliseconds = sprintf('%03d', ($nowForm - floor($nowForm)) * 1000);
         $formattedDate = date('ymdHis.' . $milliseconds, $nowForm);
         $nomImg = strstr($emtoAi, ' ', true) . "_" . $model . "_" . $formattedDate . ".png";
         $dirImg = './imagenes/';
         $dirNimg = $dirImg.$nomImg;
         $input_file = $dirNimg;
         $caption = $emTai;
         file_put_contents($dirNimg, $imageData);
         add_caption_to_png_metadata($input_file, $input_file, $caption);
         $imageFiles[] = $nomImg;
         $image_str .= $imgUrl . $nomImg . ",";
         }

      $emAsu = "Imagen de Dall-e Cortesia de: " . $emtoAi . " via https://ia.1-s.es ";
      $miMsg = "<p><< - >>".$emtoAi."<< - >>".$remMsg."<< - >>".$emDat."<< - From - >>".$emIp." - ".$now."<< - Greetings ;)</p>";  
        // Start output buffering
      ob_start();
      if ($emUsr !== '') {
      sense_mail($emtoAi, $emUsr, $remMsg, $emAsu, $miMsg, $imageFiles, $nomImg);
       }
      foreach ($imageFiles as $nomImg) {
         if ($smsNum !== '' && $somApi !=='') {
            sense_sms($remMsg, $nomImg, $smsNum, $somApi);
         }
      }
     $debugOutput = ob_get_contents();
     ob_end_clean();
     file_put_contents('OUTPUTLOCO.log', $debugOutput, FILE_APPEND);

     $logon = "<+>".$emDat."<->".$model."<->".$size."<->".$numimg."<->".$emtoAi."<->".$emIp."<->".date(" d-m-Y.H:i:s :)")."<+>".PHP_EOL;
     foreach ($imageFiles as $nomImg) {
     $logon = $logon."<I>\n"."https://iabots.1-s.es/dall-e/imagenes/".$nomImg."\n<I>".PHP_EOL;
     }

     $logNow = $logon;
     $temp = file_get_contents('Dall-e2.log');
     $logFull = $logNow.$temp;
     file_put_contents('Dall-e2.log', $logFull);

  } else {
       echo 'Error: '.PHP_EOL.$response.$userin;
       $emIp = $_SERVER['REMOTE_ADDR'];
       $rapidcode = str_replace(array("\n", "\r"), '', $response);
       $logon = "<-->".$rapidcode."<->".$emIp."<->".$emDat."<->".$model."<->".$size."<->".$numimg."<->".$emtoAi."<->".date(" d-m-Y H:i:s :)<-->").PHP_EOL;
       file_put_contents('Dall-e2.log', $logon, FILE_APPEND); 
  }

   $aiRES = array(
       'ready' => true
   );
   header('Content-Type: application/json');
   echo json_encode($aiRES); 
   // Redirect back to the HTML page with the 'image_urls' parameter
   header("Location: index.html?image_urls=" . $image_str);
  exit;
}
?>
