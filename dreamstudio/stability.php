<?php
date_default_timezone_set('Europe/Madrid');
global $emip, $imgUrl, $dirImg, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $imageNoms, $imageNom, $smsNum, $somApi, $mHost, $mUsr, $mPas, $mDe, $Bcc, $mMsg;

require_once '@DIRYNOMSENSEMAIL';
require_once '@DIRYNOMSENSESMS';
require_once '@DIRYNOMSISVARS';
    // Stability Diffusion form by 1wise.es
    // http://ia.1-s.es
    // http://1wise.es
    //
    // Last edit 16-04-03-2023 00:00
    //
function wrap_text($text, $max_width = 80) {
    return wordwrap($text, $max_width, "\n", true);
}

function add_caption_to_png_metadata($input_file, $output_file, $caption) {
    // Wrap the caption to ensure lines are no longer than 80 characters
    $wrapped_caption =  wrap_text($caption);

    // Call the Python script with the input file path, output file path, and wrapped caption as arguments
    $output = shell_exec("python3 @DIRYNOMADDCAPTION " . escapeshellarg($input_file) . " " . escapeshellarg($output_file) . " " . escapeshellarg($wrapped_caption));

    return $output;
}
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] == true) {
    global $emip, $imgUrl, $dirImg, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $imageNoms, $imageNom, $smsNum, $somApi, $mHost, $mUsr, $mPas, $mDe, $Bcc, $mMsg;

    $emIp = $_SERVER['REMOTE_ADDR'];
    $emRem = $_POST['emrem'];
    $nowForm = date(" d-m-Y H:i:s ");

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
    if (preg_match('~@GREPINSMS(.*?)@GREPOUTSMS~', $emRem, $smsNch)) {
        $smsNum = $smsNch[1];
    }
    $remMsg = '';
    if (preg_match('~_mSg_(.*?)_mSg_~', $emRem, $mstch)) {
        $remMsg = $mstch[1];
    }
    $emDat = $_POST['prompt'];
    $cfg_scale = $_POST['cfg_scale'];
    $height = $_POST['height'];
    $width = $_POST['width'];
    $weight = $_POST['weight'];
    $numImg = $_POST['numimg'];
    $steps = $_POST['steps'];
    $seed = $_POST['seed'];
    $clip = $_POST['clip'];
    $model = $_POST['model'];
    $apiKey = $_POST['api-key'];
    $apiUrl = 'https://api.stability.ai/v1/generation/'.$model.'/text-to-image';
    $sampler = $_POST['sampler'];
    $capAi = array(
       'Content-Type: application/json',
       'Accept: application/json',
       'Authorization: Bearer ' . $apiKey,
    );

     $data = array(
       "text_prompts" => [
       [
       "text" => $emDat,
       "weight" => floatval($weight),
       ]
      ],
     "cfg_scale" => intval($cfg_scale),
     "clip_guidance_preset" => $clip,
     "height" => intval($height),
     "width" => intval($width),
     "sampler" => ($sampler == "I") ? null : $sampler,
     "samples" => intval($numImg),
     "seed" => intval($seed),
     "steps" => intval($steps),
     );
    ob_start();
    $aiCurl = curl_init();
    curl_setopt($aiCurl, CURLOPT_URL, $apiUrl);
    curl_setopt($aiCurl, CURLOPT_POST, 1);
    curl_setopt($aiCurl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($aiCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($aiCurl, CURLOPT_HEADER, 0);
    curl_setopt($aiCurl, CURLOPT_HTTPHEADER, $capAi);
    $aiRes = curl_exec($aiCurl);
    $httpcode = curl_getinfo($aiCurl, CURLINFO_HTTP_CODE);
    curl_close($aiCurl);
    $image_str = '';
    $image_log = '';
    if ($httpcode == 200) {
         $result = json_decode($aiRes, true);
         $nowForm = date(' d-m-y H:i:s ');
         $emIp = $_SERVER['REMOTE_ADDR'];
         $artifacts = $result['artifacts'];
         $numsimgs = '1';
         foreach ($artifacts as $artifact) {
            global $imgUrl, $dirImg; 
            $image = $artifact['base64'];
            $finishReason = $artifact['finishReason'];
            $seed = $artifact['seed'];
            $decodedImageData = base64_decode($image);
            $imgcont = date('-ymdHis.').$numsimgs .".".$numImg.".".$seed;
            $nomImg = strstr($emtoAi, ' ', true)."-".$model.$imgcont.".png";
            $dirNimg = $dirImg.$nomImg;
            $input_file = $dirNimg;
            $imageNom = $nomImg;
            $imageNoms[] = $imageNom;
            file_put_contents($dirNimg, $decodedImageData); // Decoded base64 image data
            $emTai = $emtoAi." ".$model.": ".$emDat." seed:".$seed;
            $caption = $emTai;
            add_caption_to_png_metadata($input_file, $input_file, $caption);
            $numsimgs = intval($numsimgs) + 1;
            $image_str .= $imgUrl.$nomImg.",";
            $image_log .= $imgUrl.$nomImg."\n<ImagenUrl>\n";
            sleep(1);
         }
         $emAsu = "Imagen de ".$model." Cortesia de: ".$emtoAi." via ".$sitUrl;
         $miMsg = "<p><< - >>".$emtoAi."<< - >>".$remMsg."<< - >>".$emDat."<< - From - >>".$emIp." - ".date(' d-m-y H:i:s ')."<< - Greetings ;)</p>";
         if ($emUsr !== '') {
            sense_mail($emIp, $emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $dirImg, $imgUrl, $imageNoms, $imageNom, $mMsg, $mHost, $mUsr, $mPas, $mDe, $mBcc);
         }
         foreach ($imageNoms as $imageNom) {
           if ($smsNum !== '' && $somApi !=='') {
             sense_sms($imgUrl, $remMsg, $imageNom, $smsNum, $somApi);
                $image_str .= $imgUrl.$nomImg.",";
            $image_log .= $imgUrl.$nomImg."\n<ImagenUrl>\n";
           }
         }
         $logon = "<Prompt>".PHP_EOL.$emDat.PHP_EOL."<M> ".$model." <T> ".$height."x".$width." <N> ".$numImg." <A> ".$emtoAi." <+> ".$emIp." <f> ".$finishReason."<->".$nowForm."<+> ;)".PHP_EOL."<ImagenUrl ..seed.png>".PHP_EOL;
         $logNow = $logon.$image_log.PHP_EOL;
         $temp = file_get_contents('@NOMSTABILITYLOG');
         $logFull = $logNow.$temp;
         file_put_contents('@NOMSTABILITYLOG', $logFull);
 } else {
       echo 'Error: '.$httpcode.PHP_EOL;
       $emIp = $_SERVER['REMOTE_ADDR'];
       $logon = "<-->".$httpcode."<->".$seed.$emIp."<->".$emDat."<->".$model."<->".$height."x".$width."<->".$numImg."<->".$emtoAi."<->.".$finishReason."<->".$nowForm.PHP_EOL;
       file_put_contents('@NOMSTABILITYLOG', $logon, FILE_APPEND); 
 }

 $aiRes = array(
 'ready' => true
 );
    // Redirect back to the HTML page with the 'image_urls' parameter
 ob_clean();
 header("Location: index.html?image_urls=" . $image_str);
exit;
}
?>
