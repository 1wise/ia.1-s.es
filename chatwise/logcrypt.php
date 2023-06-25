<?php
	// ChatWise Form
	// https://ia.1-s.es/
	// https://1wise.es
	// https://www-alawise.es/1-s
	// Last edit 25-06-2023 00:00
	//
	// Print a standard page header
	//
  $wise = "El Wise: ";
  $leDatReg = '';
  $pfCrypt = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carReg = "@DIRREGISTROS";
    if ($_POST['aicrypt'] == '') {
      $aiCry = "@OPENAIAPIKEY";
    } else {
    $aiCry = $_POST['aicrypt'];
    }
    $wise = $_POST['wise'];
    $emRem = $_POST['emRem'];
    $anemCrypt =  $emRem.":".$wise.":".$aiCry;
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
   <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>
   <style>
    * {
    font-family: 'Open Sans';
    }
    .textbox2 {
     resize: both;
     height: 300px;
     width: 680px;
    }
   </style>
   <title>ChatWise</title>
</head>
  <body>
    <form method="post">
       <h1>Consultas al Wise</h1>
       <label id="nulog">Tu Conversacion con<?php echo ": ".$wise." - "; ?><?php echo " - ".$pfCrypt." - "; ?></lable><br>
       <button type="button" style="width:80px; hight:30px; font-size:16pt;" id="copyleDat" onclick="ToClipboard()">Copiar</button><br>
       <textarea name="rescrypt" style="font-size:14px;" class="textbox2" readonly><?php echo htmlspecialchars($leDatReg); ?></textarea><br>
       <input type="text" style="width:475px; font-size:12pt;" id="aicrypt" name="aicrypt" placeholder="Clave, dejar en blanco para usar Clave @EMPRESA">
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
       </select><br>
       <input type="text" style="width:500px; hight:30px; font-size:14pt;" id="emRem" name="emRem" placeholder="¿Quien consulto al Wise?" required><br>
       <input type="submit" style="width:250px; font-size:20pt;"  name="submit" value="Consultar"><br>
   </form>
 <script>
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
 </script>
</body>
</html>
