<?php
global  $emRem, $emUsr, $model, $system_msg, $emIp, $aimPar, $aimSgem, $smsNum, $somApi;
require_once './sense_mail.php';
require_once './sense_sms.php';
	// ChatGPT Form
	// http://ia.1-s.es/
	// http://1wise.es
	//
	// Last edit 08-04-2023 00:00
	//
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $somUsu = '';
    $somPas = '';
    $somApi = base64_encode("$somUsu:$somPas");
    $carReg = "./registros/";
    $aiCry = $_POST['aicrypt'];
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
    if (preg_match('~_lolI_(.*?)_Ipop_~', $remNom, $smsNch)) {
        $smsNum = $smsNch[1];
    }

    $anemCrypt =  $emRem.":".$model.":".$aiCry;
    if ($model == "gpt-3.5-turbo" || $model == "gpt-3.5-turbo-0301") {
    $aiUrl = 'https://api.openai.com/v1/chat/completions';
  } else {
    $aiUrl = 'https://api.openai.com/v1/completions';
  }
  
  $headers = array(
  'Content-Type: application/json',
  'Authorization: Bearer ' . $aiCry,
  );

  // Set the request data
  if ($model == "gpt-3.5-turbo" || $model == "gpt-3.5-turbo-0301") {
      $data = array(
      "model" => $model,
      "messages" => [
          ["role" => "system", "content" => $system_msg],
          ["role" => "user", "content" => $user_msg],
          ["role" => "assistant", "content" => $assistant_msg],
          ["role" => "user", "content" => $prompt]
      ],
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

    // Check if it's Babbage or ChatGPT-3.5-turbo based on the JSON structure
    if (isset($decaiRes['choices'][0]['text'])) {
    // Babbage
    $aiCont = $decaiRes['choices'][0]['text'];
    } elseif (isset($decaiRes['choices'][0]['message']['content'])) {
    // ChatGPT-3.5-turbo
    $aiCont = $decaiRes['choices'][0]['message']['content'];
    } else {
    $aiCont = "Error: Unknown JSON structure";
    }

    // Trim the extracted content to fit the SMS character limit of 154 characters
    $aiPar = $decaiRes['choices'][0]['finish_reason'];
    $aiReslim = htmlspecialchars($aiCont, ENT_QUOTES);
    $aimSgem = str_replace(
    ["?>", "<?", '\"', "\\r", "\\n", "\n\n", "\n\n"],
    ["?.>", "<.?", '"', "\n", "\n", "\n", "\n"],
    $aiReslim
    );

    $metCrypt = "aes-256-cbc";
    $ivSize = openssl_cipher_iv_length($metCrypt);
    $iv = substr(md5($anemCrypt), 0, $ivSize); 
    $pfCrypt = $carReg.md5($anemCrypt).".log";
    $datReg = $emRem.": ".$system_msg."\nUser: ".$user_msg."\naAssistant: ".$assistant_msg."\nResult: ".$model." + ".$aimSgem." - ".$aiPar." - ".$emIp." - ".$now.PHP_EOL;
    $datRegCrypt = openssl_encrypt($datReg, $metCrypt, $anemCrypt, 0, $iv);
    file_put_contents($pfCrypt, $datRegCrypt.PHP_EOL, FILE_APPEND);
    $leDatReg = '';
    $lines = file($pfCrypt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $leDatReg .= openssl_decrypt($line, $metCrypt, $anemCrypt, 0, $iv);
    }
    $emAsu = "Respuesta de ".$model.", Cortesia de: " . $emRem . " via https://ia.1-s.es ";
    $miMsg .= "<->".$emSg."<-><br><p> ".$emRem." pregunta a ".$model.": <br>".$system_msg."<br>  ".$model.": ".$aimSgem." - ".$aiPar."<br>  - ".$emIp." - ".$now."<br> !! Geetings !! ;)</p>";  

   if ($emUsr !== '') {
       sense_mail($emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp);
   }
   if ($smsNum !== '' && $somApi !=='') { 
      sense_sms($aimSgem, $model, $smsNum, $somApi);
   }       
     $logon =  "<+> ".$emRem.": ".$system_msg.PHP_EOL.$model.": ".$aimSgem." - ".$aiPar."\n  - ".$emIp." - ".$now."<+>".PHP_EOL;  
     $logNow = $logon;
     $temp = file_get_contents('MUYLOCO.log');
     $logFull = $logNow.$temp;
     file_put_contents('MUYLOCO.log', $logFull);
}
?>
<!DOCTYPE html>
<html lang="es">
 <head>
   <meta name="Peticion a GPT" content="width=device-width; height=device-height; charset=utf-8;">
   <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
   <meta http-equiv="Pragma" content="no-cache">
   <meta http-equiv="Expires" content="0">
  <style>
    .textbox1 {
    resize: none;
    height: 100px;
    width: 680px;
    }
    .textbox2 {
    resize: none;
    height: 200px;
    width: 680px;
    }
    .button1 {
    font-size: 20pt;
    margin-left: 42px;
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
  </style>
</head>
 <title>Peticion a la API ChatGPT</title>
<body>
  <form method="post">
    <h1>Peticion a la API ChatGPT<a href="https://iabots.1-s.es/chatgpt/logcrypt.php" target="_blank" rel="noreferrer noopener" class="button-link">Consultar logs API</a></h1>
    <input type="text" style="width:680px; hight:30px; font-size:14pt;" id="aicrypt" name="aicrypt" placeholder="Clave API de OpenAI" required><br>
    <select style="font-size:14pt;" name="model" id="model" required>
     <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
     <option value="gpt-3.5-turbo-0301">gpt-3.5-turbo-0301</option>
     <option value="text-davinci-003">text-davinci-003</option>
     <option value="text-davinci-002">text-davinci-002</option>
     <option value="code-davinci-002">code-davinci-002</option>
     <option value="davinci">davinci</option>
     <option value="curie">curie</option>
     <option value="babbage">babbage</option>
     <option value="ada">ada</option>
    </select>
    
     <input type="text" style="width:500px; hight:30px; font-size:14pt;" id="emrem" name="emrem" placeholder="Usuario, orientativo para GPT"><br>
    
     <label style="font-size:14pt;">Max Tokens 0 a 4000: <input type="text" style="width:50px; font-size:14pt;" maxlength="4" name="maxtokens" value="300"></lable>&nbsp;
    
     <label style="font-size:14pt;">Temp 0 a 2: <input type="text" style="width:50px; font-size:14pt;" maxlength="3" name="temperature" value="0"></lable>&nbsp;

     <label style="font-size:14pt;" id="top_p">top_p 0 a 1: <input type="text" style="width:50px; font-size:14pt;" id="top_p" maxlength="3" name="top_p" value="0" ></lable><br>

     <textarea style="font-size:14px;" class="textbox2" name="system_msg" id="system_msg" rows="20" placeholder="Prompt Sistema: para modelos otros que gpt 3.5 solo se llena este campo"></textarea><br>
  
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="font-size:14pt;"><i>Presence -1 a 1: </i><input type="text" style="width:50px; font-size:14pt;" id="presence_penalty" maxlength="4" name="presence_penalty" value="0" ></lable>&nbsp;&nbsp;&nbsp;

    <label style="font-size:14pt;"><i>Frequency -1 a 1: </i><input type="text" style=" width:50px; font-size:14pt;" id="frequency_penalty" maxlength="4" name="frequency_penalty" value="0" ></lable>

    <input type="submit" style="font-size:20pt; margin-left:50px;" name="submit" value="Consultar GPT"><br>
    <textarea name="response" style="font-size:14px;" class="textbox2" placeholder="Assistant:" readonly><?php echo $emRem.": ".$system_msg."\n".$model.": ".$aimSgem." - ".$now; ?></textarea><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=" font-size:14pt;">Tu Conversacion con : <?php echo $model; ?></lable><br>
    <textarea name="rescrypt" style="font-size:14px;" class="textbox2" readonly><?php echo $leDatReg; ?></textarea><br>
    
    <textarea style="font-size:14pt;" class="textbox1" name="user_msg" id="user_msg" placeholder="User:"></textarea><br>
    
    <textarea style="font-size:14pt;" class="textbox1" name="assistant_msg" id="assistant_msg" placeholder="Assistant:"></textarea><br>
    
    <textarea style="font-size:14pt;" class="textbox1" name="prompt" id="prompt" placeholder="User:"></textarea><br>
</body>
</html>

