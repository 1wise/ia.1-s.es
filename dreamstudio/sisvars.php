<?php
global $imgUrl, $dirImg, $somApi, $mHost, $mUsr, $mPas, $mDe, $Bcc, $mMsg;

$appUrl = "@URLAPP";
$sitApp = "@URLSITAPP";
$sitUrl = "@SITURL";
$imgUrl = "@URLDIRIMAGENES";
$dirImg = '@DIRIMG';
$emEmp = '@EMPRESA';
$mHost = '@PMSERVERDOMAIN';
$mUsr = '@PMUSERDOMAIN';
$mPas = '@PMPASSOWRD';
$mDe  = '@PMUSERDOMAIN';
$mBcc = '@PMBCC';
$mMsg = "<html><body>";
$mMsg .= "<p>Este correo ha sido enviado desde el formulario de ".$sitUrl." powered by - ".$emEmp." - ";
$mMsg .= "Consulta a Stability.ai API por - ".$sitApp." para movil - ".$appUrl." - ";
$mMsg .= "Todo el Archivo de Imagenes - ".$imgUrl. " -</p>";

$somUsu = '@SMSUSER';
$somPas = '@SMSPASSWORD';
$somApi = base64_encode("$somUsu:$somPas");
?>
