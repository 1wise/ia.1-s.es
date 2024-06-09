<?php
set_time_limit(300);
date_default_timezone_set('Europe/Madrid');
global $emtoAi, $emUsr, $remMsg, $emAsu, $miMsg, $remMsg, $imageNoms, $imageNom, $smsNum, $somApi;
require_once '@DIRYNOMSENSEMAIL';
require_once '@DIRYNOMSENSESMS';

// Dall-e2 form by 1wise.es
// http://ia.1-s.es
// http://1wise.es
//
// Last edit 08-06-2024 00:00
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
$somUsu = '@SMSUSER';
$somPas = '@SMSPASSWORD';
$somApi = base64_encode("$somUsu:$somPas");
$result = '';
$image_str = '';
$tempFilePath = '';
$im = '';
$httpcode = '-';
$totalSize = 0;
$fileCount = 0;
$fileName = '';
$fileNameMask = '';
$archImagen = '';
$archMascara = '';
$prompt = '';
$apiUrl = '';
$capAI = '';
$encdata = '';
$aiCurl = '';
$model = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] == true) {
    $model = $_POST['model'];
    $emRem = $_POST['emrem'];
    $size = $_POST['size'];
    $prompt = $_POST['prompt'];
    $numimg = $_POST['numimg'];
    $archImagen = $_FILES['archImagen'];
    $archMascara = $_FILES['archMascara'];
    $emtoAi = '';
    $apiKey = $_POST['api-key'];
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
    if ($_POST['tipocon'] == 'generations' && isset($_POST['prompt'])) {
        $emTai = $emtoAi." con ".$model." ".$prompt;
        $nowForm = date(' d-m-y H:i:s ');
        $apiKey = $_POST['api-key'];
        $apiUrl = 'https://api.openai.com/v1/images/generations';
        $capAi = array(
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        );
        $data = array(
            'model' => $model,
            'prompt' => $prompt,
            'size' => $size,
            'n' => intval($numimg),
            'response_format' => 'b64_json',
            'user' => $emtoAi,
        );
        $encdata = json_encode($data);
    } elseif ($_POST['tipocon'] == 'editsConMas' && isset($_FILES['archImagen']) && isset($_FILES['archMascara']) && isset($_POST['prompt'])) {
         $totalSize = $_FILES['archImagen']['size'];
         $tmpFilePath = $_FILES['archImagen']['tmp_name'];
         $fileName = $_FILES['archImagen']['name'];
         $totalSizeMask = $_FILES['archMascara']['size'];
         $tmpFilePathMask = $_FILES['archMascara']['tmp_name'];
         $fileNameMask = $_FILES['archMascara']['name'];
         $emTai = $emtoAi." con ".$model." ".$prompt;
         $nowForm = date(' d-m-y H:i:s ');
         $apiKey = $_POST['api-key'];
         if ($tmpFilePath != "" && is_uploaded_file($tmpFilePath) && $totalSize <= 4000000 && $tmpFilePathMask != "" && is_uploaded_file($tmpFilePathMask) && $totalSizeMask <= 4000000) {
           $targetPath = './uploads/' . $fileName;
           move_uploaded_file($tmpFilePath, $targetPath);
           $targetPathMask = './uploads/' . $fileNameMask;
           move_uploaded_file($tmpFilePathMask, $targetPathMask);
           $apiUrl = 'https://api.openai.com/v1/images/edits';
           $capAi = array(
              'Authorization: Bearer ' . $apiKey,
            );
           $data = array(
              'model' =>  "dall-e-2",
              'image' => new CURLFile($targetPath),
              'mask' => new CURLFile($targetPathMask),
              'prompt' => $prompt,
              'size' => $size,
              'n' => intval($numimg),
              'response_format' => 'b64_json',
              'user' => $emtoAi,
            );
            $encdata = $data;
        }
    } elseif ($_POST['tipocon'] == 'editsSinMas' && isset($_FILES['archImagen']) && isset($_POST['prompt'])) {
         $totalSize = $_FILES['archImagen']['size'];
         $tmpFilePath = $_FILES['archImagen']['tmp_name'];
         $fileName = $_FILES['archImagen']['name'];
         $emTai = $emtoAi." con ".$model." ".$prompt;
         $nowForm = date(' d-m-y H:i:s ');
         $apiKey = $_POST['api-key'];
      if ($tmpFilePath != "" && is_uploaded_file($tmpFilePath) && $totalSize <= 4000000) {
           $targetPath = './uploads/' . $fileName;
           move_uploaded_file($tmpFilePath, $targetPath);
           $apiUrl = 'https://api.openai.com/v1/images/edits';
           $capAi = array(
              'Authorization: Bearer ' . $apiKey,
            );
           $data = array(
              'model' =>  "dall-e-2",
              'image' => new CURLFile($targetPath),
              'prompt' => $prompt,
              'size' => $size,
              'n' => intval($numimg),
              'response_format' => 'b64_json',
              'user' => $emtoAi,
            );
            $encdata = $data;
         }
     } elseif ($_POST['tipocon'] == 'variations' && isset($_POST['prompt'])) {
         $totalSize = $_FILES['archImagen']['size'];
         $tmpFilePath = $_FILES['archImagen']['tmp_name'];
         $fileName = $_FILES['archImagen']['name'];
         if ($tmpFilePath != "" && is_uploaded_file($tmpFilePath) && $totalSize <= 4000000) {
           $targetPath = './uploads/' . $fileName;
           move_uploaded_file($tmpFilePath, $targetPath);
           $emTai = $emtoAi." con ".$model." ".$prompt;
           $nowForm = date(' d-m-y H:i:s ');
           $apiKey = $_POST['api-key'];
           $apiUrl = 'https://api.openai.com/v1/images/variations';
           $capAi = array(
              'Authorization: Bearer ' . $apiKey,
            );
           $data = array(
              'model' =>  "dall-e-2",
              'image' => new CURLFile($targetPath),
              'size' => $size,
              'n' => intval($numimg),
              'response_format' => 'b64_json',
              'user' => $emtoAi,
            );
        $encdata = $data;
      }
     } else {
        $logon = '<-<-ERROR->->'.PHP_EOL;
        file_put_contents('Dall-e2.txt', $logon, FILE_APPEND);
        header("Location: index.html" . $image_str);
        exit;
     }
     ob_start();
   if ($apiUrl != '' && $encdata != '' && $capAi != '') {
     $aiCurl = curl_init($apiUrl);
     curl_setopt($aiCurl, CURLOPT_POST, 1);
     curl_setopt($aiCurl, CURLOPT_POSTFIELDS, $encdata);
     curl_setopt($aiCurl, CURLOPT_HTTPHEADER, $capAi);
     curl_setopt($aiCurl, CURLOPT_RETURNTRANSFER, true);
     $aiRES = curl_exec($aiCurl);
     $httpcode = curl_getinfo($aiCurl, CURLINFO_HTTP_CODE);
     curl_close($aiCurl);
  }
 }
  if ($httpcode == 200) {
      $nowForm = date('d-m-y H:i:s');
        $emIp = $_SERVER['REMOTE_ADDR'];
        $result = json_decode($aiRES, true);
        $datas = $result['data'];
        $imageNoms = array();
        $numsimgs = '1';
        $image_str = '';
        $image_log = '';
        foreach ($datas as $dato) {
            $imgUrl = "@URLIMG";
            $image = $dato['b64_json'];
            $decodedImageData = base64_decode($image);
            $imgcont = date('ymdHis.').$numsimgs .".".$numimg;
            $semtoAi = strstr($emtoAi, ' ', true);
            if ($semtoAi == '') {
              $nomImg = $emtoAi."-Dall-E-".$imgcont.".png";
            } else {
              $nomImg = strstr($emtoAi, ' ', true)."-Dall-E-".$imgcont.".png";
            }
            $dirImg = '@DIRIMG';
            $dirNimg = $dirImg.$nomImg;
            file_put_contents($dirNimg, $decodedImageData);
            $input_file = $dirNimg;
            $caption = $emTai;
            $imageNom = $nomImg;
            $imageNoms[] = $imageNom;
            add_caption_to_png_metadata($input_file, $caption);
            $numsimgs = intval($numsimgs) + 1;
            $image_str .= $imgUrl . $nomImg . ",";
            $image_log .= "<I>  " . $imgUrl . $nomImg . "  <I>\n";
            sleep(1);
        }
        $image_str = substr($image_str, 0, -1);
        $emAsu = "Imagen de Dall-e Cortesia de: ".$emtoAi." via @EMPRESA ";
        $miMsg = "<p><< - >>".$emtoAi."<< - >>".$remMsg."<< - >>".$prompt." - ".$fileName." - ".$fileNameMask." - ".$tipocon."<< - From - >>".$emIp." - ".$nowForm."<< - Greetings ;)</p>";

        if ($emUsr !== '') {
            sense_mail($emtoAi, $emUsr, $emAsu, $miMsg, $remMsg, $imageNoms, $imageNom);
        }

        foreach ($imageNoms as $imageNom) {
            if ($smsNum !== '' && $somApi !=='') {
                sense_sms($remMsg, $imageNom, $smsNum, $somApi);
            }
        }

        $logon = "<+>". $prompt." - ".$fileName." - ".$fileNameMask." - ".$tipocon." <+>"."<->".$size."<->".$numimg."<->".PHP_EOL.$emtoAi."<->".$emIp."<->".$nowForm."<+>".PHP_EOL;
        $logNow = $logon . $image_log;
        $temp = file_get_contents('@NOMDALLELOG');
        $logFull = $logNow . $temp;
        file_put_contents('@NOMDALLELOG', $logFull);
   } else {
        $emIp = $_SERVER['REMOTE_ADDR'];
        $logon = "<-ERROR->".$httpcode."<->".$emIp."<->".$prompt." - ".$fileName." - ".$fileNameMask." - ".$tipocon."<->".$size."<->".$numimg."<->".$emtoAi."<->".$nowForm.PHP_EOL;
        file_put_contents('@NOMDALLELOG', $logon, FILE_APPEND);
   }
    $aiRES = array(
        'ready' => true
    );

 // Redirect back to the HTML page with the 'image_urls' parameter
   ob_clean();
   header("Location: index.html?image_urls=" . $image_str);
  exit;
?>


