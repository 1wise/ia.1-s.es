<!DOCTYPE html>
<html>
<head>
  <title>Todo el Archivo de Imagenes</title>
</head>
<body>

<?php
	$dir = "./"; // path to your image folder
	$files = scandir($dir); // get all the files in the folder
	foreach($files as $file) {
		if(substr($file, -4) == ".png") { // only display png files
		echo '<div style="display: inline-block;"><img src="' . $file . '" alt="' . $file . '"></div>';
		}
	}
?>
</body>
</html>
