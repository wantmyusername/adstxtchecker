<!DOCTYPE html>
<html>
<head>
  <title>Contenido de ads.txt</title>
</head>
<body>
  <textarea id="contenidoAds" rows="10" cols="50"><?php
    $dominio = $_GET['dominio'];
    $url = "http://" . $dominio . "/ads.txt";
    $tempFilePath = "/tmp/ads.txt";

    $fileContent = file_get_contents($url);
    
    file_put_contents($tempFilePath, $fileContent);

    $tempFileContent = file_get_contents($tempFilePath);

    echo $tempFileContent;
  ?></textarea>

  <textarea id="estado" rows="3" cols="50"><?php
    $lines = explode("\n", $tempFileContent);
    $errors = false;

    foreach ($lines as $line) {
      $line = trim($line);
      if (substr($line, 0, 1) === '#') {
        continue;
      }
      $fields = explode(',', $line);
      if (count($fields) >= 3 && count($fields) <= 4) {
        $field1 = trim($fields[0]);
        $field2 = trim($fields[1]);
        $field3 = trim($fields[2]);
        
        $pattern = '/^[^,\s]+,\s*[^,\s]+,\s*[^,\s]+(?:,\s*[^,\s]+)?$/';
        if (!preg_match($pattern, $line)) {
          // Error: formato incorrecto en los campos
          echo "Se encontró un error en la línea: " . $line . "\n";
          $errors = true;
        }
      } elseif (!empty($line)) {
        echo "Se encontró un error en la línea: " . $line . "\n";
        $errors = true;
      }
    }
    if (!$errors) {
      echo "No se encontraron errores en el archivo ads.txt";
    }
  ?></textarea>
</body>
</html>
