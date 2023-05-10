<?php
global  $emRem, $emUsr, $model, $system_msg, $emIp, $aimPar, $aimSgem, $smsNum, $somApi;
require_once '@DIRYNOMSENSEMAIL';
require_once '@DIRYNOMSENSESMS';
	// ChatGPT Form
	// http://ia.1-s.es/
	// http://1wise.es
	//
	// Last edit 11-05-2023 00:00
	//
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $somUsu = '@SMSUSER';
    $somPas = '@SMSPASSWORD';
    $somApi = base64_encode("$somUsu:$somPas");
    $carReg = "@DIRREGISTROS";
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
    if ($model == "gpt-3.5-turbo" || $model == "gpt-3.5-turbo-0301" || $model == "gpt-4" || $model == "gpt-4-0314" || $model == "gpt-4-32k" || $model == "gpt-4-32k-0314") {
    $aiUrl = 'https://api.openai.com/v1/chat/completions';
  } else {
    $aiUrl = 'https://api.openai.com/v1/completions';
  }
  
  $headers = array(
  'Content-Type: application/json',
  'Authorization: Bearer ' . $aiCry,
  );

  // Set the request data
  if ($model == "gpt-3.5-turbo" || $model == "gpt-3.5-turbo-0301" || $model == "gpt-4" || $model == "gpt-4-0314" || $model == "gpt-4-32k" || $model == "gpt-4-32k-0314") {
      $data = array(
      "model" => $model,
      "messages" => [
        ["role" => "system", "content" => $system_msg, "name" => $remAut],
        ["role" => "user", "content" => $user_msg, "name" => $remAutU],
        ["role" => "assistant", "content" => $assistant_msg, "name" => $remAutA],
        ["role" => "user", "content" => $prompt, "name" => $remAutP]
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
    $datReg = $emRem.": ".$system_msg."\nUser: ".$user_msg."\nAssistant: ".$assistant_msg."\nUser: ".$prompt."\n".$model.": ".$aimSgem."\n<+> ".$aiPar." - ".$emIp." - ".$now.PHP_EOL;
    $datRegCrypt = openssl_encrypt($datReg, $metCrypt, $anemCrypt, 0, $iv);
    file_put_contents($pfCrypt, $datRegCrypt.PHP_EOL, FILE_APPEND);
    $leDatReg = '';
    $lines = file($pfCrypt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $leDatReg .= openssl_decrypt($line, $metCrypt, $anemCrypt, 0, $iv);
    }
    $emAsu = "Respuesta de ".$model.", Cortesia de: " . $emRem . " via https://ia.1-s.es ";
    $miMsg .= "<->".$emSg."<->\n ".$emRem." pregunta a ".$model.": \n".$system_msg."\nUser: \n".$user_msg."\nAssitant: \n".$assistant_msg."\nUser: \n".$prompt."\n  ".$model.": ".$aimSgem." - ".$aiPar."\n  - ".$emIp." - ".$now."\n !! Geetings !! ;)\n";
   if ($emUsr !== '') {
       sense_mail($emRem, $emUsr, $model, $system_msg, $emAsu, $miMsg, $emIp);
   }
   if ($smsNum !== '' && $somApi !=='') { 
      sense_sms($aimSgem, $model, $smsNum, $somApi);
   }       
     $logon =  "<+> ".$emRem.": ".$system_msg.PHP_EOL.$model.": ".$aimSgem." - ".$aiPar."\n  - ".$emIp." - ".$now."<+>".PHP_EOL;  
     $logNow = $logon;
     $temp = file_get_contents('@NOMGPTLOG');
     $logFull = $logNow.$temp;
     file_put_contents('@NOMGPTLOG', $logFull);
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
    resize: both;
    height: 100px;
    width: 680px;
    }
    .textbox2 {
    resize: both;
    height: 200px;
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
  </style>
</head>
 <title>Peticion a la API ChatGPT</title>
<body>
  <form method="post">
    <h1>Peticion a la API ChatGPT<a href="@URLLOGCRYPT@NOMLOGCRYPT" target="_blank" rel="noreferrer noopener" class="button-link">Consultar logs API</a></h1>
    <input type="text" style="width:485px; hight:30px; font-size:12pt;" id="aicrypt" name="aicrypt" placeholder="Clave API de OpenAI" required>
    <select style="font-size:14pt;" name="model" id="model" required>
     <option value="gpt-4">gpt-4</option>
     <option value="gpt-4-0314">gpt-4-0314</option>
     <option value="gpt-4-32k">gpt-4-32k</option>
     <option value="gpt-4-32k-0314">gpt-4-32k-0314</option>
     <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
     <option value="gpt-3.5-turbo-0301">gpt-3.5-turbo-0301</option>
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
    <input type="text" style="width:400px; hight:30px; font-size:14pt;" id="emrem" name="emrem" placeholder="Usuario, orientativo para GPT">
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emaut" name="emaut" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>
    <label style="font-size:14pt;">M.Tok: <input type="text" style="width:55px; font-size:14pt;" maxlength="5" name="maxtokens" value="300"></lable>&nbsp;&nbsp;&nbsp;&nbsp;
    <label style="font-size:14pt;">Temp: <input type="text" style="width:50px; font-size:14pt;" maxlength="3" name="temperature" value="0" placeholder="0 2"></lable>&nbsp;&nbsp;&nbsp;&nbsp;
    <label style="font-size:14pt;" id="top_p">top_p: <input type="text" style="width:50px; font-size:14pt;" id="top_p" maxlength="3" name="top_p" value="0"  placeholder="0 1"></lable>     
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="font-size:14pt;"><i>Pres: </i><input type="text" style="width:50px; font-size:14pt;" id="presence_penalty" maxlength="4" name="presence_penalty" value="0" placeholder="-1 1" ></lable>&nbsp;&nbsp;&nbsp;&nbsp;
    <label style="font-size:14pt;"><i>Freq: </i><input type="text" style=" width:50px; font-size:14pt;" id="frequency_penalty" maxlength="4" name="frequency_penalty" value="0" placeholder="-1 1"></lable><br>
    <textarea style="font-size:14px;" class="textbox2" name="system_msg" id="system_msg" rows="20" placeholder="Prompt Sistema: para modelos otros que gpt 3.5 y superiores, solo se llena este campo"></textarea><br>
    <input type="submit" style="width:680px; font-size:20pt;" name="submit" value="Consultar ChatGPT"><br>
    <textarea name="response" style="font-size:14px;" class="textbox2" placeholder="Assistant:" readonly><?php echo $emRem.": ".htmlspecialchars($system_msg)."\n".$model.": ".htmlspecialchars($aimSgem)." - ".$now; ?></textarea><br>   
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=" font-size:14pt;">Tu Conversacion con : <?php echo $model; ?></lable><br>
    <textarea name="rescrypt" style="font-size:14px;" class="textbox2" readonly><?php echo htmlspecialchars($leDatReg); ?></textarea><br>
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emautu" name="emautu" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>  
    <textarea style="font-size:14px;" class="textbox1" name="user_msg" id="user_msg" placeholder="User:"></textarea><br>    
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emauta" name="emauta" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>
    <textarea style="font-size:14px;" class="textbox1" name="assistant_msg" id="assistant_msg" placeholder="Assistant:"></textarea><br>   
    <input type="text" style="width:266px; hight:30px; font-size:14pt;" id="emautp" name="emautp" placeholder="Autor mensaje a-z A-Z 0-9 _"><br>
    <textarea style="font-size:14px;" class="textbox1" name="prompt" id="prompt" placeholder="User:"></textarea><br>
</body>
</html>

