<?php
  $missatge = '';
  $smsNum = '';
  $somUsu = '@SMSUSER';
  $somPas = '@SMSPASSWORD';
  $somApi = base64_encode("$somUsu:$somPas");
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emIp = $_SERVER['REMOTE_ADDR'];
    $now = date("d-m-Y H:i:s");
    $smsNum = $_POST['mobils'];
    $missatge = $_POST['missatge'];
    $somMsg = $_POST['data'];
    $validesa = intval($_POST['validesa']);
    $prioritat = 1;
    $imSgem = str_replace(
       ['\'', '’', '‘', '“', '”', '–', '—', '…', '¢', '€', '©', '®', '™', '°', '²', '³', 'µ', '¹', '¼', '½', '¾', '×', '÷',
        '«', '»', 'Á', 'â', 'ã', 'È', 'ê', 'ë', 'ì', 'ï', 'ð', 'Ì', 'í', 'Î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ÿ', 'ù',
        'ú', 'û', 'ü', 'ý', 'Ý', 'Þ', 'þ', 'þ', 'æ', 'Æ', 'œ', 'Œ', 'Š', 'š', 'Ÿ', 'Ž', 'ž'],
       ['\'', '\'', '"', '"', '-', '-', '.', '?', 'E', '(', 'R', 'T', 'o', '2', '3', 'u', '1', '1/4', '1/2', '3/4', '*', '/',
        '<', '>', 'A', 'a', 'a', 'E', 'e', 'e', 'i', 'i', 'o', 'I', 'i', 'I', 'i', 'o', 'o', 'o', 'o', 'o', 'y', 'u',
        'u', 'u', 'u', 'y', 'Y', 'P', 'p', 'p', 'e', 'E', 'e', 'E', 'S', 's', 'Y', 'Z', 'z' ], $missatge);
    $imSgem = html_entity_decode($imSgem, ENT_QUOTES, 'UTF-8');

    $sinMsg = $imSgem;

    // Split the message by spaces or line breaks
    $words = preg_split('/\s+/', $sinMsg);

    // Initialize variables
    $smsPart = "";
    $smsParts = [];

    // Iterate through the words and build SMS parts
    foreach ($words as $word) {
        $tempPart = $smsPart . ($smsPart === "" ? "" : " ") . $word;

        // If the temporary part is within the 154 character limit, add the word
        if (strlen($tempPart) <= 154) {
            $smsPart = $tempPart;
        } else {
            // If the temporary part exceeds the limit, store the current part and start a new one
            $smsParts[] = $smsPart;
            $smsPart = $word;
        }
    }

    // Add the last part to the parts array
    if ($smsPart !== "") {
        $smsParts[] = $smsPart;
    }

    // Send each SMS part
    foreach ($smsParts as $part) {
        // Your existing SMS sending logic goes here

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
                   'missatge' => $part,
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
          $smsLog  = ">".$emIp."<< - >>".$smsNum."<< - >>".$missatge."<< - >>".$somRes." - ".date("d-m-Y H:i:s :)").PHP_EOL;
          file_put_contents('@NOMSOMSMSLOG', $smsLog, FILE_APPEND | LOCK_EX); 

          if ($status_som === 200) {
           echo " - SMS Enviado con Exito !!".$somRes.PHP_EOL;
          } else {
          echo " - Fallo envio SMS !!".$somRes.PHP_EOL;
          }
          echo " - Enviado SMS: $part\n".PHP_EOL;
    }
 }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Enviar SMS</title>
    <style>
      .comment {
        resize: both;
        height: 200px;
        width: 400px;
      }
    </style>

    <script>
     function countCharacters() {
      var textarea = document.getElementById("missatge");
      var count = textarea.value.length;
      var counter = document.getElementById("characterCount");
      var sets = Math.floor(count / 154);
      var remaining = count % 154;
      counter.innerHTML = sets + " sets of 154 characters, and in 154 ser character count[" + remaining + "/154]";
     }
    </script>
</head>
<body>
  <h2>Enviar SMS</h2>
  <form id="request-form" method="post" enctype="multipart/form-data">
    <b><label style="font-size:14pt;" for="mobils">Numeros Separats per ;</label></b><br>
    <textarea style="font-size:14pt; width:400px; height:200px" type="text" id="mobils" name="mobils" placeholder="376360121"><?php echo $smsNum; ?></textarea><br><br>
    <b><label style="font-size:14pt;" for="missatge">Missatge:</label></b><br><br>
    <textarea class="comment" style="font-size:14pt;" id="missatge" name="missatge" oninput="countCharacters()"><?php echo $missatge; ?></textarea>
    <p id="characterCount">0/154</p>
    <b><label style="font-size:14pt;" for="data">Data</label></b>
    <b><input style="font-size:14pt;" type="text" id="data" name="data" value="<?php echo date("d/m/Y", time()); ?>"></b><br><br>
    <b><label style="font-size:14pt;" for="validesa">Validesa:</label></b>
    <b><input style="font-size:14pt;" type="text" id="validesa" name="validesa" value="60" maxlength="2"></b><br><br>
    <b><input style="font-size:14pt;" type="submit" value="Enviar SMS"></b>
  </form>
</body>
</html>

