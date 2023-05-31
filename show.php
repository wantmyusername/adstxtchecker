<!DOCTYPE html>
<html>
<head>
  <title>Contenido de ads.txt</title>
  <style>
    .container {
      display: flex;
      flex-wrap: wrap;
    }

    #col-50 {
      width: 50%;
      box-sizing: border-box;
      padding: 10px;
    }

    #contenidoAds {
      height: 200px;
      width: 100%;
      box-sizing: border-box;
      resize: none;
    }

    #estado {
      padding: 10px;
      border: 1px solid #ccc;
    }

    #resultado {
      font-weight: bold;
      margin-bottom: 5px;
    }
    textarea.numbered {
        background: url(http://i.imgur.com/2cOaJ.png);
        background-attachment: local;
        background-repeat: no-repeat;
        padding-left: 35px;
        padding-top: 10px;
        border-color:#ccc;
    }
  </style>
  <style>
    @media (max-width: 600px) {
      #col-50 {
        width: 100%;
      }
    }
  </style>
</head>
<body>
    
<?php
$dominio = $_GET['dominio'];

if (strpos($dominio, 'http://') === 0 || strpos($dominio, 'https://') === 0) {
  echo "Verifica la URL o prueba nuevamente.";
  exit;
}

$urls = array(
  "https://" . $dominio . "/ads.txt",
  "http://" . $dominio . "/ads.txt"
);

$tempFilePath = "/tmp/ads.txt";

foreach ($urls as $url) {
  // Incluir el protocolo "http://" en la URL
  $url = str_replace("https://", "http://", $url);

  try {
    // Descargar el archivo ads.txt temporalmente
    $fileContent = file_get_contents($url);

    // Guardar el contenido en un archivo temporal
    file_put_contents($tempFilePath, $fileContent);

    // Leer el contenido del archivo temporal
    $tempFileContent = file_get_contents($tempFilePath);

    // Si se llega a este punto, se ha descargado correctamente el archivo
    break;
  } catch (Exception $e) {
    // Capturar cualquier excepción ocurrida al intentar descargar el archivo
    // y continuar con la siguiente URL
    continue;
  }
}

$logFilePath = "log.txt";

// Abrir el archivo de registro en modo de escritura (agregar contenido al final)
$logFile = fopen($logFilePath, 'a');

// Obtener la fecha y hora actual
$timestamp = date("Y-m-d H:i:s");

// Obtener la IP del cliente
$ip = $_SERVER['REMOTE_ADDR'];

// Formatear la fila con la información del dominio, fecha, hora y dirección IP
$logRow = "$dominio\t$timestamp\t$ip" . PHP_EOL;

// Escribir la fila en el archivo de registro
fwrite($logFile, $logRow);

// Cerrar el archivo de registro
fclose($logFile);

?>


  <div class="container">
    <div id="col-50">
      <h4>Contenido del archivo ads.txt <?php echo $dominio; ?></h4>
      <textarea id="contenidoAds" class="numbered" rows="10" cols="50" readonly><?php echo $tempFileContent; ?></textarea>
    </div>

    <div id="col-50">
      <h4>Estado del archivo</h4>
      <div id="estado">
        <?php
          $lines = explode("\n", $tempFileContent);
          $errors = false;
          $lineNumber = 1;

          foreach ($lines as $line) {
            $line = trim($line);

            // Ignorar líneas de comentario
            if (substr($line, 0, 1) === '#') {
              $lineNumber++;
              continue;
            }

            // Verificar si es una línea de registro de datos
            $fields = explode(',', $line);
            if (count($fields) >= 3 && count($fields) <= 4) {
              $field1 = trim($fields[0]);
              $field2 = trim($fields[1]);
              $field3 = trim($fields[2]);

              // Verificar espacios y comas en los campos
              $pattern = '/^[^,\s]+,\s*[^,\s]+,\s*[^,\s]+(?:,\s*[^,\s]+)?$/';
              if (!preg_match($pattern, $line)) {
                // Error: formato incorrecto en los campos
                echo "⚠️ <b> Se encontró un error en la línea $lineNumber:</b> " . $line . "<br>";
                $errors = true;
              }
            } elseif (!empty($line)) {
              // Error: línea no válida
              echo "⚠️  <b> Se encontró un error en la línea $lineNumber:</b> " . $line . "<br>";
              $errors = true;
            }

            $lineNumber++;
          }

          // Mostrar el estado (si hay errores o no)
          if (empty($tempFileContent)) {
            echo "<div id='resultado'>⚠️ El archivo ads.txt está vacío.</div>";
            $errors = true;
          } elseif (!$errors) {
            echo "<div id='resultado'>✅ No se encontraron errores en el archivo ads.txt.<br>El archivo es accesible por AdSense</div>";
          }
        ?>
      </div>
    </div>
  </div>

  <div style="margin-top: 20px; text-align: center;">
    <button onclick="window.history.back();">Verificar otra URL</button>
  </div>
</body>
</html>
