<?php
	// ChatGPT Form
	// http://ia.1-s.es/
	// http://1wise.es
	//
	// Last edit 14-05-2023 00:00
	//
	// Print a standard page header
	//
  $model = ';)';
  $leDatReg = '';
  $pfCrypt = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carReg = "@DIRREGISTROS";
    $aiCry = $_POST['aicrypt'];
    $model = $_POST['model'];
    $emRem = $_POST['emRem'];
    $anemCrypt =  $_POST['emRem'].":".$_POST['model'].":".$aiCry;    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $metCrypt = "aes-256-cbc";
      $ivSize = openssl_cipher_iv_length($metCrypt);
      $iv = substr(md5($anemCrypt), 0, $ivSize); 
      $pfCrypt = $carReg.md5($anemCrypt).".log";
      if (file_exists($pfCrypt)) {
        $leDatReg = '';
        $lines = file($pfCrypt, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
          $leDatReg .= openssl_decrypt($line, $metCrypt, $anemCrypt, 0, $iv);
        }
      } else {
        $leDatReg = "La combinacion es incorrecta";
      }
   }
}
?>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
   <meta http-equiv="Pragma" content="no-cache" />
   <meta http-equiv="Expires" content="0" />
   <title>Consulta Logs API ChatGPT</title>
    <style>
     .textbox2 {
      resize: both;
      height: 300px;
      width: 680px;
     }
   </style>
</head>
  <body>
    <form method="post">
       <h1>Consulta Logs API ChatGPT</h1>
       <label id="nulog">Tu Conversacion con<?php echo ": ".$model." - "; ?><?php echo " - ".$pfCrypt." - "; ?></lable><br>
       <textarea name="rescrypt" style="font-size:14px;" class="textbox2" readonly><?php echo htmlspecialchars($leDatReg); ?></textarea><br>
       <input type="text" style="width:486px; font-size:12pt;" id="aicrypt" name="aicrypt" placeholder="Clave API de OpenAI">
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
       <input type="text" style="width:500px; hight:30px; font-size:14pt;" id="emRem" name="emRem" placeholder="Usuario, orientativo para GPT"><br>
       <input type="submit" style="width:250px; font-size:20pt;"  name="submit" value="Consultar"><br>

   </form>
</body>
</html>
