<?php
set_time_limit(300);
date_default_timezone_set('Europe/Madrid');
global  $emRem, $emUsr, $model, $system_msg, $emIp, $aimPar, $aimSgem, $smsNum, $somApi;
require_once '@DIRYNOMSENSEMAIL';
require_once '@DIRYNOMSENSESMS';
	// ChatGPT Form
	// http://ia.1-s.es/
	// http://1wise.es
	//
	// Last edit 25-06-2023 00:00
	//
    $emRem = '';
    $system_msg = '';
    $user_msg = '';
    $assistant_msg = '';
    $prompt = '';
    $model = ';)';
    $aiProTok = '';
    $aiCompTok = '';
    $aiToken = '';
    $leDatReg = '';
    $aimSgem = '';
    $aiPar = '';
    $now = date(' d-m-y H:i:s ');
    $emAut = '';
    $emAutU = '';
    $emAutA = '';
    $emAutP = '';
  // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $somUsu = '@SMSUSER';
    $somPas = '@SMSPASSWORD';
    $somApi = base64_encode("$somUsu:$somPas");
    $carReg = "@DIRREGISTROS";
    if ($_POST['aicrypt'] == '') {
      $aiCry = "@OPENAIAPIKEY";
    } else {
    $aiCry = $_POST['aicrypt'];
    }
    $model = $_POST['model'];
    $system_msg = $_POST['system_msg'];
    $user_msg = $_POST['user_msg'];
    $assistant_msg = $_POST['assistant_msg'];
    $prompt = $_POST['prompt'];
    $maxtokens = intval($_POST['maxtokens']);
    $temperature = floatval($_POST['temperature']);
    $top_p = floatval($_POST['top_p']);
    $presence_penalty = floatval($_POST['presence_penalty']);
    $frequency_penalty = floatval( $_POST['frequency_penalty']);
    $remNom = $_POST['emrem'];
    if ($_POST['emaut'] == '') {
      $remAut = "_";
    } else {
    $remAut = $_POST['emaut'];
    }
    if ($_POST['emautu'] == '') {
      $remAutU = "_";
    } else {
    $remAutU = $_POST['emautu'];
    }
    if ($_POST['emauta'] == '') {
      $remAutA = "_";
    } else {
    $remAutA = $_POST['emauta'];
    }
    if ($_POST['emautp'] == '') {
      $remAutP = "_";
    } else {
    $remAutP = $_POST['emautp'];
    }
    $emRem = '';
    if (strpos($remNom, "-") !== false ) {
       $emRem = strstr($remNom, '-', true);
    } else {
       $emRem = $remNom;
    }
    $emUsr = '';
    if (preg_match('~_m_(.*?)_m_~', $remNom, $emUsrch)) {
      $emUsr = $emUsrch[1];
    }
    $emSg = '';
    if (preg_match('~_mSg_(.*?)_mSg_~', $remNom, $emSgch)) {
     $emSg = $emSgch[1];
    }
    $smsNum = '';
    if (preg_match('~_@GREPINSMS_(.*?)_@GREPOUTSMS_~', $remNom, $smsNch)) {
        $smsNum = $smsNch[1];
    }

    $anemCrypt =  $emRem.":".$model.":".$aiCry;
    if ($model == "gpt-3.5-turbo" || $model == "gpt-3.5-turbo-16k" || $model == "gpt-3.5-turbo-0613" || $model == "gpt-3.5-turbo-16k-0613" || $model == "gpt-4" || $model == "gpt-4-0613" || $model == "gpt-4-32k" || $model == "gpt-4-32k-0613") {
    $aiUrl = 'https://api.openai.com/v1/chat/completions';
  } else {
    $aiUrl = 'https://api.openai.com/v1/completions';
  }

  $headers = array(
  'Content-Type: application/json',
  'Authorization: Bearer ' . $aiCry,
  );

  // Set the request data
if ($model == "gpt-3.5-turbo" || $model == "gpt-3.5-turbo-16k" || $model == "gpt-3.5-turbo-0613" || $model == "gpt-3.5-turbo-16k-0613" || $model == "gpt-4" || $model == "gpt-4-0613" || $model == "gpt-4-32k" || $model == "gpt-4-32k-0613") {
    $messages = [];
    if (!empty($system_msg)) {
        $messages[] = ["role" => empty($user_msg) && empty($assistant_msg) && empty($prompt) ? "user" : "system", "content" => $system_msg, "name" => $remAut];
    }
    if (!empty($user_msg)) {
        $messages[] = ["role" => "user", "content" => $user_msg, "name" => $remAutU];
    }
    if (!empty($system_msg) && !empty($aimSgem) && !empty($user_msg)) {
        $messages[] = ["role" => "assistant", "content" => $aimSgem, "name" => $remAutA];
    } elseif (!empty($assistant_msg)) {
        $messages[] = ["role" => "assistant", "content" => $assistant_msg, "name" => $remAutA];
    }
    if (!empty($prompt)) {
        $messages[] = ["role" => "user", "content" => $prompt, "name" => $remAutP];
    }
    $data = array(
        "model" => $model,
        "messages" => $messages,
        "max_tokens" => $maxtokens,
        "temperature" => $temperature,
        "top_p" => $top_p,
        "n" => 1,
        'stop' => null,
        "user" => $emRem,
        "presence_penalty" => $presence_penalty,
        "frequency_penalty" => $frequency_penalty,
    );
  } else {
      $data = array(
      "user" => $emRem,
      "model" => $model,
      "prompt" =>  $system_msg,
      'stop' => null,
      "max_tokens" => $maxtokens,
      "temperature" => $temperature,
       );
   }
    $intArr = curl_init();

    curl_setopt_array($intArr, array(
    CURLOPT_URL => $aiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => $headers,
    ));

    // La respuesta
    $now = date(' d-m-y H:i:s ');
    $emIp = $_SERVER['REMOTE_ADDR'];
    $aiRes = curl_exec($intArr);

    // Assuming $aiRes is the JSON response from ChatGPT
    $decaiRes = json_decode($aiRes, true); // Decoding JSON into an associative array

    // Check the JSON structure based on the available keys
    if (isset($decaiRes['choices'][0]['text'])) {
        // Babbage
        $aiCont = $decaiRes['choices'][0]['text'];
    } elseif (isset($decaiRes['choices'][0]['message']['content'])) {
        // ChatGPT-3.5-turbo, GPT-4, GPT-4-0314, GPT-4-32k, or GPT-4-32k-0314
        $aiCont = $decaiRes['choices'][0]['message']['content'];
    } else {
        $aiCont = $aiRes." - Error: Unknown JSON structure";
    }
    // Trim the extracted content to fit the SMS character limit of 154 characters
    $aiPar = $decaiRes['choices'][0]['finish_reason'];
    $aiProTok = $decaiRes['usage']['prompt_tokens'];
    $aiCompTok = $decaiRes['usage']['completion_tokens'];
    $aiToken = $decaiRes['usage']['total_tokens'];
    $aiReslim = $aiCont;
    $aimSgem = str_replace(
    ["?>", "<?", '\"', "\\r", "\\n", "\n\n", "\n\n"],
    ["?.>", "<.?", '"', "\n", "\n", "\n", "\n"],
    $aiReslim
    );
    $metCrypt = "aes-256-cbc";
    $ivSize = openssl_cipher_iv_length($metCrypt);
    $iv = substr(md5($anemCrypt), 0, $ivSize);
    $pfCrypt = $carReg.md5($anemCrypt).".log";
    $datReg = $emRem.": ".$system_msg."\n";
    if (!empty($user_msg)) {
        $datReg .= "User: ".$user_msg."\n";
    }
    if (!empty($assistant_msg)) {
        $datReg .= "Assistant: ".$assistant_msg."\n";
    }
    if (!empty($prompt)) {
        $datReg .= "User: ".$prompt."\n";
    }
    $datReg .= $model.": ".$aimSgem."\n<+> ".$aiPar." - ".$aiProTok." - ".$aiCompTok." - ".$aiToken." - ".$emIp." - ".$now.PHP_EOL;
    $datRegCrypt = openssl_encrypt($datReg, $metCrypt, $anemCrypt, 0, $iv);
    file_put_contents($pfCrypt, $datRegCrypt.PHP_EOL, FILE_APPEND);
    $leDatReg = '';
    $lines = file($pfCrypt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $leDatReg .= openssl_decrypt($line, $metCrypt, $anemCrypt, 0, $iv);
    }
    if ($emUsr !== '') {
      $emAsu = "Respuesta de ".$model.", Cortesia de: " . $emRem . " via @EMPRESA ";
      $miMsg = "<->".$emSg."<->\n ".$emRem." pregunta a ".$model.": \n".$system_msg."\n";
      if (!empty($user_msg)) {
          $miMsg .= "User: ".$user_msg."\n";
      }
      if (!empty($assistant_msg)) {
          $miMsg .= "Assistant: ".$assistant_msg."\n";
      }
      if (!empty($prompt)) {
          $miMsg .= "User: ".$prompt."\n";
      }
      $miMsg .= $model.": ".$aimSgem."\n".$aiPar." - ".$emIp." - ".$now."\n !! Geetings !! ;)\n";
      sense_mail($emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp);
   }
   if ($smsNum !== '' && $somApi !=='') {
      sense_sms($aimSgem, $model, $smsNum, $somApi, $emIp);
   }
   $logon =  "<+> ".$emRem.": ".$system_msg.PHP_EOL.$model.": ".$aimSgem."\n".$aiPar." - ".$emIp." - ".$now."<+>".PHP_EOL;
   $logNow = $logon;
   $temp = file_get_contents('@NOMGPTLOG');
   $logFull = $logNow.$temp;
   file_put_contents('@NOMGPTLOG', $logFull);
   $uso = "<+> ".$emRem." - ".$now." <P>".$aiProTok."<C>".$aiCompTok."<T>".$aiToken."\n";
   $tempuso = file_get_contents('@NOMUSOGPTLOG');
   $usofull = $uso.$tempuso;
   file_put_contents('@NOMUSOGPTLOG', $usofull);
}
?>
<!DOCTYPE html>
<html lang="es">
 <head>
   <meta name="Peticion a GPT" content="width=device-width; height=device-height; charset=utf-8;">
   <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
   <style>
    * {
    font-family: 'Open Sans';
    }
    .textbox1 {
    resize: both;
    height: 120px;
    width: 680px;
    }
    .button-link {
    display: inline-block;
    font-size: 16pt;
    text-align: right;
    color: #FFF;
    background-color: #4CAF50;
    border: none;
    padding: 10px 20px;
    text-decoration: none;
    cursor: pointer;
    border-radius: 4px;
    margin-left: 40px;
    }
    .button-link:hover {
    background-color: #45a049;
    }
    .loader {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
 </style>
</head>
 <title>Peticion a la API ChatGPT</title>
<body>
  <form accept-charset="UTF-8" id="request-form" method="post" enctype="multipart/form-data">
    <h1><a href="@URLAPP">Peticion a la API ChatGPT</a><a href="@URLLOGCRYPT@NOMLOGCRYPT" target="_blank" rel="noreferrer noopener" class="button-link">Consultar logs API</a></h1>
    <input type="text" style="width:440px; hight:30px; font-size:12pt;" id="aicrypt" name="aicrypt" placeholder="Clave API de OpenAI  dejar en blanco para usar Clave de @EMPRESA">
    <select style="font-size:14pt;" name="model" id="model" required>
     <option value="gpt-4">gpt-4-8k</option>
     <option value="gpt-4-0613">gpt-4-8k-0613</option>
     <option value="gpt-4-32k">gpt-4-32k</option>
     <option value="gpt-4-32k-0613">gpt-4-32k-0613</option>
     <option value="gpt-3.5-turbo">gpt-3.5-turbo-4k</option>
     <option value="gpt-3.5-turbo-16k">gpt-3.5-turbo-16k</option>
     <option value="gpt-3.5-turbo-0613">gpt-3.5-turbo-4k-0613</option>
     <option value="gpt-3.5-turbo-16k-0613">gpt-3.5-turbo-16k-0613</option>
     <option value="text-davinci-003">text-davinci-003</option>
     <option value="text-davinci-002">text-davinci-002</option>
     <option value="text-curie-001">text-curie-001</option>
     <option value="text-babbage-001">text-babbage-001</option>
     <option value="text-ada-001">text-ada-001</option>
     <option value="davinci-instruct-beta">davinci-instruct-beta</option>
     <option value="davinci">davinci</option>
     <option value="curie-instruct-beta">curie-instruct-beta</option>
     <option value="curie">curie</option>
     <option value="babbage">babbage</option>
     <option value="ada">ada</option>
    </select><br>
    <input type="text" style="width:390px; hight:30px; font-size:14pt;" id="emrem" name="emrem" value="<?php echo $emRem; ?>" placeholder="Usuario, orientativo para GPT" required>
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emaut" name="emaut" value="<?php echo $emAut; ?>" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>
    <label style="font-size:14pt;">M.Tok: <input type="text" style="width:55px; font-size:14pt;" maxlength="5" name="maxtokens" value="4096"></lable>&nbsp;
    <label style="font-size:14pt;">Temp: <input type="text" style="width:50px; font-size:14pt;" maxlength="3" name="temperature" value="0" placeholder="0 2"></lable>&nbsp;
    <label style="font-size:14pt;" id="top_p">top_p: <input type="text" style="width:50px; font-size:14pt;" id="top_p" maxlength="3" name="top_p" value="0"  placeholder="0 1"></lable>
    <label style="font-size:14pt;"><i>Pres: </i><input type="text" style="width:50px; font-size:14pt;" id="presence_penalty" maxlength="4" name="presence_penalty" value="0" placeholder="-1 1" ></lable>&nbsp;
    <label style="font-size:14pt;"><i>Freq: </i><input type="text" style=" width:50px; font-size:14pt;" id="frequency_penalty" maxlength="4" name="frequency_penalty" value="0" placeholder="-1 1"></lable><br>
    <textarea style="font-size:14px;" class="textbox1" name="system_msg" id="system_msg" rows="20" placeholder="Prompt Sistema: para modelos otros que gpt 3.5 y superiores, solo rellenar este campo" required><?php echo $system_msg; ?></textarea><br>
    <input style="text-align:center; width:570px; font: Arial; font-size:16pt" id="submit" type="submit" name="submit" value="Consulta ChatGPT NO darle cuando esta en ROJO">&nbsp;&nbsp;
    <button type="button" style="width:100px; hight:30px; font-size:16pt;" id="copyButton" onclick="copyToClipboard()">Copiar</button><br>
    <textarea name="response" style="font-size:14px;" class="textbox1" readonly><?php
        echo $emRem.": ".str_replace(['<?','?>'],['<.?','?.>'],htmlspecialchars($system_msg))."\n";
        if (!empty($user_msg)) {
            echo "User: ".str_replace(['<?','?>'],['<.?','?.>'],htmlspecialchars($user_msg))."\n";
        }
        if (!empty($assistant_msg)) {
            echo "Assistant: ".str_replace(['<?','?>'],['<.?','?.>'],htmlspecialchars($assistant_msg))."\n";
        }
        if (!empty($prompt)) {
            echo "User: ".str_replace(['<?','?>'],['<.?','?.>'],htmlspecialchars($prompt))."\n";
        }
        echo $model.": ".htmlspecialchars($aimSgem)."\n".$aiPar." - ".$aiProTok." - ".$aiCompTok." - ".$aiToken." - ".$now;
    ?></textarea><br>
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emautu" name="emautu" value="<?php echo $emAutU; ?>" placeholder="Autor mensaje a-z A-Z 0-9 _">
    <button type="button" style="width:200px; hight:30px; font-size:16pt;" id="copyasg" onclick="asgToClipboard()">Copiar respuesta</button><br>
    <textarea style="font-size:14px;" class="textbox1" name="user_msg" id="user_msg" placeholder="User:"><?php echo $user_msg; ?></textarea><br>
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emauta" name="emauta"value="<?php echo $emAutA; ?>" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>
    <textarea style="font-size:14px;" class="textbox1" name="assistant_msg" id="assistant_msg" placeholder="Assistant:"><?php
     if (!empty($_POST['assistant_msg']) || empty($_POST['user_msg'])) {
       echo str_replace(['<?','?>'],['<.?','?.>'],htmlspecialchars($assistant_msg));
     } else {
       echo htmlspecialchars($aimSgem);
     }
    ?></textarea><br>
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emautp" name="emautp" value="<?php echo $emAutP; ?>" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>
    <textarea style="font-size:14px;" class="textbox1" name="prompt" id="prompt" placeholder="User:"><?php
     if (!empty($_POST['prompt'])) {
        echo str_replace(['<?','?>'],['<.?','?.>'],htmlspecialchars($prompt));
     }
    ?></textarea><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="font-size:14pt;">Tu Conversacion con : <?php echo $model; ?></lable>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" style="width:200px; hight:30px; font-size:16pt;" id="copyleDat" onclick="ToClipboard()">Copiar el log</button><br>
    <textarea name="rescrypt" style="font-size:14px;" class="textbox1" readonly><?php echo htmlspecialchars($leDatReg); ?></textarea><br>
    <textarea hidden name="aimSgem" id="aimSgem"><?php echo htmlspecialchars($aimSgem); ?></textarea>
    <div id="loader" class="loader" style="display: none;"></div>
 <script>
  function showLoader() {
    const loader = document.getElementById('loader');
    const submitButton = document.getElementById('submit');
    loader.style.display = 'block';
    submitButton.style.backgroundColor = 'red';
  }
  function hideLoader() {
    const loader = document.getElementById('loader');
    const submitButton = document.getElementById('submit');
    loader.style.display = 'none';
    submitButton.style.backgroundColor = '';
  }
  function copyToClipboard() {
    const responseTextarea = document.querySelector('textarea[name="response"]');
    const tempInput = document.createElement('textarea');
    document.body.appendChild(tempInput);
    tempInput.value = responseTextarea.value;
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    alert(responseTextarea.value);
  }
  function ToClipboard() {
    const ledatregTextarea = document.querySelector('textarea[name="rescrypt"]');
    const ledatInput = document.createElement('textarea');
    document.body.appendChild(ledatInput);
    ledatInput.value = ledatregTextarea.value;
    ledatInput.select();
    document.execCommand('copy');
    document.body.removeChild(ledatInput);
    alert(ledatregTextarea.value);
  }
  function asgToClipboard() {
    const asgTextarea = document.querySelector('textarea[name="aimSgem"]');
    const asgInput = document.createElement('textarea');
    document.body.appendChild(asgInput);
    asgInput.value = asgTextarea.value;
    asgInput.select();
    document.execCommand('copy');
    document.body.removeChild(asgInput);
    alert(asgTextarea.value);
  }
  document.getElementById('request-form').addEventListener('submit', function() {
    showLoader();
  });
  document.getElementById('request-form').addEventListener('load', function() {
    hideLoader();
   });
 </script>
 </form>
</body>
</html>
