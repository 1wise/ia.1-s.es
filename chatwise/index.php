<?php
set_time_limit(300);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Madrid');
global  $emRem, $emUsr, $wise, $system_msg, $emIp, $aimPar, $aimSgem, $smsNum, $somApi;
require_once './sense_mail.php';
require_once './sense_sms.php';
	// ChatWise Form
	// https://ia.1-s.es/
	// https://1wise.es
	// https://www.alawise.es/1-s
	// Last edit 20-05-2023 00:00
	//
  $emRem = '';
  $user_msg = '';
  $assistant_msg = '';
  $prompt = '';
  $model = ';)';
  $leDatReg = '';
  $aimSgem = '';
  $aiPar = '';
  $wise = "El Wise";
  $now = date(' d-m-y H:i:s ');
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
    $wise = $_POST['wise'];
    $system_msg = "You're a ChatBot based on the written works and known history of ".$_POST['wise'].", respond as if it would be him, in ".$_POST['estilo']." ".$_POST['idioma']." language.";
    $user_msg = $_POST['user_msg'];
    $assistant_msg = $_POST['assistant_msg'];
    $prompt = $_POST['prompt'];
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
    if (preg_match('~__@GREPINSMS__(.*?)__@GREPOUTSMS__~', $remNom, $smsNch)) {
        $smsNum = $smsNch[1];
    }
    $anemCrypt =  $emRem.":".$wise.":".$aiCry;
    $aiUrl = 'https://api.openai.com/v1/chat/completions';
    $headers = array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $aiCry,
    );

    // Set the request data
    $messages = [];
    if (!empty($system_msg)) {
        $messages[] = ["role" => empty($user_msg) && empty($assistant_msg) && empty($prompt) ? "user" : "system", "content" => $system_msg];
    }
    if (!empty($user_msg)) {
        $messages[] = ["role" => "user", "content" => $user_msg];
    }
    if (!empty($system_msg) && !empty($aimSgem) && !empty($user_msg)) {
        $messages[] = ["role" => "assistant", "content" => $aimSgem];
    } elseif (!empty($assistant_msg)) {
        $messages[] = ["role" => "assistant", "content" => $assistant_msg];
    }
    if (!empty($prompt)) {
        $messages[] = ["role" => "user", "content" => $prompt];
    }
    $data = array(
        "model" => $model,
        "messages" => $messages,
        "max_tokens" => intval(1024),
        "temperature" => floatval(0.4),
        "top_p" =>  floatval(0.2),
        "n" => 1,
        'stop' => null,
        "user" => $emRem,
        "presence_penalty" => floatval(0),
        "frequency_penalty" => floatval(0),
    );
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
    if (isset($decaiRes['choices'][0]['message']['content'])) {
        $aiCont = $decaiRes['choices'][0]['message']['content'];
    } else {
        $aiCont = $aiRes." - Error: Unknown JSON structure";
    }
    // Trim the extracted content to fit the SMS character limit of 154 characters
    $aiPar = $decaiRes['choices'][0]['finish_reason'];
    $aiReslim = $aiCont;
    $aimSgem = str_replace(
    ['\"', "\\r", "\\n", "\n\n", "\n\n"],
    ['"', "\n", "\n", "\n", "\n"],
    $aiReslim
    );
    $metCrypt = "aes-256-cbc";
    $ivSize = openssl_cipher_iv_length($metCrypt);
    $iv = substr(md5($anemCrypt), 0, $ivSize);
    $pfCrypt = $carReg.md5($anemCrypt).".log";
    $datReg = $emRem.": ".$user_msg."\n";
    if (!empty($assistant_msg)) {
        $datReg .= $wise.": ".$assistant_msg."\n";
    }
    if (!empty($prompt)) {
        $datReg .= $emRem.": ".$prompt."\n";
    }
    $datReg .= $wise.": ".$aimSgem."\n<+> ".$aiPar." - ".$emIp." - ".$now.PHP_EOL;
    $datRegCrypt = openssl_encrypt($datReg, $metCrypt, $anemCrypt, 0, $iv);
    file_put_contents($pfCrypt, $datRegCrypt.PHP_EOL, FILE_APPEND);
    $leDatReg = '';
    $lines = file($pfCrypt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $leDatReg .= openssl_decrypt($line, $metCrypt, $anemCrypt, 0, $iv);
    }
    if ($emUsr !== '') {
      $emAsu = "Respuesta de ".$wise.", Cortesia de: " . $emRem . " via @EMPRESA ";
      $miMsg = "<->".$emSg."<->\n ".$emRem." pregunta a ".$wise.": \n".$user_msg."\n";
      if (!empty($assistant_msg)) {
          $miMsg .= $wise.": ".$assistant_msg;
      }
      if (!empty($prompt)) {
          $miMsg .= $emRem.": ".$prompt;
      }
      $miMsg .= $wise.": ".$aimSgem."\n".$aiPar." - ".$emIp." - ".$now."\n !! Geetings !! ;)\n";
      sense_mail($emRem, $emUsr, $wise, $system_msg, $emAsu, $miMsg, $emIp);
   }
   if ($smsNum !== '' && $somApi !=='') {
      sense_sms($aimSgem, $model, $smsNum, $somApi, $emIp);
   }
   $logon =  "<+> ".$emRem.": ".$system_msg.PHP_EOL.$model.": ".$aimSgem."\n".$aiPar." - ".$emIp." - ".$now."<+>".PHP_EOL;
   $logNow = $logon;
   $temp = file_get_contents('@NOMGPTLOG');
   $logFull = $logNow.$temp;
   file_put_contents('@NOMGPTLOG', $logFull);
}
?>
<!DOCTYPE html>
<html lang="es">
 <head>
   <meta name="Consulta al Wise" content="width=device-width; height=device-height; charset=utf-8;">
  <style>
    h1 {
    font-size: 28pt;
    color: green;
    }
    .textbox1 {
    resize: both;
    height: 120px;
    width: 680px;
    }
    .textbox2 {
    resize: both;
    height: 240px;
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
 <title>Cosulta al Wise</title>
<body>
  <form accept-charset="UTF-8" id="request-form" method="post" enctype="multipart/form-data">
    <h1><a href="@SITURL@">Consulta al Wise</a><a href="@URLLOGCRYPT@NOMLOGCRYPT" target="_blank" rel="noreferrer noopener" class="button-link">Consultar logs</a></h1>
    <select style="font-size:14pt;" name="model" id="model" required>
     <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
     <option value="gpt-3.5-turbo-0301">gpt-3.5-turbo-0301</option>
     <option value="gpt-4">gpt-4</option>
     <option value="gpt-4-0314">gpt-4-0314</option>
    </select>
    <input type="text" style="width:495px; hight:30px; font-size:12pt;" id="aicrypt" name="aicrypt" placeholder="Clave, dejar en blanco para usar Clave de @EMPRESA"><br>
     <select style="font-size:14pt;" name="wise" id="wise" required>
     <option value="Lao Tzu">Lao Tzu</option>
     <option value="Sun Tzu">Sun Tzu</option>
     <option value="King Hammurabi">El Rey Hammurabi</option>
     <option value="Niccolo Maquiavelli">Nicolas Maquiavelo</option>
     <option value="Kahil Gibrian">Kahil Gibrian</option>
     <option value="Charles Baudelaire">Charles Baudelaire</option>
     <option value="William Shakespeare">William Shakespeare</option>
     <option value="Miguel de Cervantes">Miguel de Cervantes</option>
     <option value="Timothy Leary">Timothy Leary</option>
     <option value="Aldous Huxley">Aldous Huxley</option>
     <option value="Hermann Heisse">Hermann Heisse</option>
     <option value="Rudyard Kipling">Rudyard Kipling</option>
     <option value="Jiddu Krishnamurti">Jiddu Krishnamuri</option>
     <option value="Helena Blavatsky">Helena Blavatsky</option>
     <option value="Dale Carnegie">Dale Carnegie</option>
     <option value="Thomas Kempis">Thomas Kempis</option>
     <option value="Dante Alighieri">Dante Alighieri</option>
     <option value="Rig Vedas">Rig Vedas</option>
     <option value="Kalevalan Runes">Kalevalan Runes</option>
     <option value="Lao Tzu, Sun Tzu, King Hammurabi, Niccolo Maquiavelli, Kahil Gibrian, Charles Baudelaire, William Shakespeare, Miguel de Cervantes, Timothy Leary, Rudyard Kipling, Dale Carnegie, Thomas Kempis and Dante Alighieri">El Wise</option>
    </select>
     <select style="font-size:14pt;" name="idioma" id="idioma" required>
     <option value="Spanish">Castellano</option>
     <option value="English">Ingles</option>
     <option value="French">Frances</option>
     <option value="Catalan">Catalan</option>
    </select>
     <select style="font-size:14pt;" name="estilo" id="estilo" required>
     <option value="formal">Formal</option>
     <option value="casual">Informal</option>
    </select>
    <input type="text" style="width:265px; hight:30px; font-size:14pt;" id="emrem" name="emrem" value="<?php echo $emRem; ?>" placeholder="Usuario orientativo para el Wise" required><br>
    <input style="text-align:center; width:680px; font: Arial; font-size:16pt" id="submit" type="submit" name="submit" value="Consulta al Wise NO darle cuando esta en ROJO"><br>
    <textarea style="font-size:14px;" class="textbox1" name="user_msg" id="user_msg" placeholder="Escribe aqui tu consulta" required><?php echo $user_msg; ?></textarea><br>
    <textarea style="font-size:14px;" class="textbox1" name="assistant_msg" id="assistant_msg" placeholder="<?php echo $wise; ?>"><?php echo htmlspecialchars($aimSgem); ?></textarea><br>
    <textarea style="font-size:14px;" class="textbox1" name="prompt" id="prompt" placeholder="Escribe aqui tu replica"></textarea><br>
    <textarea style="font-size:14px;" class="textbox2" name="response" id="response" placeholder="<?php echo $wise; ?>" readonly><?php
        echo $emRem.": ".htmlspecialchars($user_msg)."\n";
        if (!empty($assistant_msg)) {
            echo $wise.": ".htmlspecialchars($assistant_msg)."\n";
        }
        if (!empty($prompt)) {
            echo $emRem.": ".htmlspecialchars($prompt)."\n";
        }
        echo $wise.": ".htmlspecialchars($aimSgem)."\n".$aiPar." - ".$now;
    ?></textarea><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="color: green; font-size:14pt;">Tu Conversacion con : <?php echo $wise; ?></lable><br>
    <textarea name="rescrypt" style="font-size:14px;" class="textbox2" readonly><?php echo htmlspecialchars($leDatReg); ?></textarea><br>
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
      submitButton.style.backgroundColor = ''; // Reset the background color
    }
    document.getElementById('request-form').addEventListener('submit', function() {
      showLoader();
    });

    document.getElementById('request-form').addEventListener('load', function() {
      hideLoader();
   });
  </script>
</body>
</html>
