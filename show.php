<!DOCTYPE html>
<html>
<head>
  <title>Contenido de ads.txt</title>
</head>
  <style>
    .container {
      display: flex;
      flex-wrap: wrap;
      border: 1px solid red;
    }

    #col-100 {
      width: 100%;
      box-sizing: border-box;
      padding: 10px;
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

// Obtener la IP del dominio
$domainIp = gethostbyname($dominio);

// Obtener información del ISP usando la API de ip-api.com
$apiUrl = "http://ip-api.com/json/" . $domainIp;
$ispInfo = file_get_contents($apiUrl);
$ispInfo = json_decode($ispInfo);

// Obtener la IP del dominio
$domainIp = gethostbyname($dominio);

// Obtener los registros DNS del dominio
$dnsRecords = dns_get_record($dominio, DNS_NS);

// Obtener los servidores de nombres (Name Servers)
$nameServers = array();
foreach ($dnsRecords as $record) {
  $nameServers[] = $record['target'];
}

// Verificar si el ISP es "cloudflare"
$isCloudflare = ($ispInfo && $ispInfo->status === "success" && $ispInfo->isp === "Cloudflare, Inc.");


// Formatear la fila con la información del dominio, fecha, hora y dirección IP
$logRow = "$dominio\t$timestamp\t$ip\t" . ($ispInfo && $ispInfo->status === "success" ? $ispInfo->isp : "") . PHP_EOL;

// Escribir la fila en el archivo de registro
fwrite($logFile, $logRow);

// Cerrar el archivo de registro
fclose($logFile);
?>



<div class="container">
  <div id="col-100">
    <h4>Contenido del archivo ads.txt <?php echo $dominio; ?></h4>
    <textarea id="contenidoAds" class="numbered" rows="10" cols="50" readonly><?php echo $tempFileContent; ?></textarea>
  </div>
</div>

<div class="container">
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
          echo "<div id='resultado'>⚠️ El archivo ads.txt está vacío o no existe.</div>";
          $errors = true;
        } elseif (!$errors) {
          echo "<div id='resultado'>✅ No se encontraron errores en el archivo ads.txt.<br>El archivo es accesible por AdSense</div>";
        }
      ?>
    </div>
    <h4>Rastreabilidad del sitio</h4>
    <div id="estado">
      <?php
        // URL del sitio web
        $siteUrl = $url_f;
        
        // User-Agent a verificar
        $userAgent = "Googlebot";
        
        // URL del archivo robots.txt
        $robotsTxtUrl = $siteUrl . "/robots.txt";
        
        // Obtener el contenido del archivo robots.txt
        $robotsTxtContent = file_get_contents($robotsTxtUrl);
        
        // Verificar si se puede rastrear el sitio para el User-Agent "Googlebot"
        $lines = explode("\n", $robotsTxtContent);
        $canCrawl = true;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (substr($line, 0, 10) === "User-agent" && stripos($line, "*") !== false) {
                $canCrawl = false;
                break;
            } elseif (stripos($line, "User-agent: Mediapartners-Google") !== false) {
                $canCrawl = true;
            } elseif (stripos($line, "User-Agent: Googlebot") !== false) {
                $canCrawl = true;
            }
        }
        // Mostrar el resultado
        if ($canCrawl) {
            echo "¡Muy bien! Tu sitio puede ser rastreado por Google";
        } else {
            echo "¡CUIDADO! Google Search y AdSense no pueden leer tu sitio. Verifica tu archivo Robots.txt";
        }
        
        ?>            
    </div>
  </div>
  <div id="col-50">
      <h4>Datos técnicos</h4>
    <div id="estado">
        <?php
        // Arreglo de protocolos y subdominios para realizar la búsqueda
        $urls_fetch = array(
            'http://' . $dominio,
            'https://' . $dominio,
        );
        
        // Bandera para indicar si se encontró el texto en alguna URL
        $encontrado = false;
        
        foreach ($urls_fetch as $url_f) {
            // Obtener el contenido HTML de la página
            $html = file_get_contents($url_f);
        
            // Buscar la cadena "adsbygoogle.js" dentro de las etiquetas <head> y </head>
            if (strpos($html, '<head>') !== false && strpos($html, '</head>') !== false) {
                // Obtener el contenido entre las etiquetas <head> y </head>
                $headContent = substr($html, strpos($html, '<head>') + 6, strpos($html, '</head>') - strpos($html, '<head>') - 6);
        
                // Buscar la cadena "adsbygoogle.js" dentro del contenido de <head>
                if (strpos($headContent, 'adsbygoogle.js') !== false) {
                    $encontrado = true;
                    echo 'Yay! El código de AdSense está correctamente instalado en ' . $url_f;
                    break;
                }
            }
        }
        
        if (!$encontrado) {
            echo '¡CUIDADO! El código de AdSense no está instalado en ' . $dominio;
        }
        ?>
    
    <hr>
      <p>La IP del dominio '<?php echo $dominio; ?>' es: <?php echo $domainIp; ?></p>
  <?php if ($ispInfo && $ispInfo->status === "success") { ?>
    <?php if ($isCloudflare || preg_match('/\.\w+\.cloudflare\.com$/', $domainIp) || preg_match('/\.\w+\.cloudflare\.com$/', implode(',', $nameServers))) { ?>
      <p style="color: red;">Advertencia: El ISP o los servidores de nombres detectados pertenecen a Cloudflare. Ten en cuenta que Cloudflare puede afectar la disponibilidad y el contenido del archivo ads.txt.</p>
    <?php } else { ?>
      <p>No se detectó Cloudflare como el ISP o los servidores de nombres.</p>
    <?php } ?>
    <b>Otros datos</b>
    <p>ISP: <?php echo $ispInfo->isp; ?></p>
    <p>City: <?php echo $ispInfo->city; ?></p>
    <p>Country: <?php echo $ispInfo->country; ?></p>
    <p>Servidores de nombres (Name Servers):</p>
    <ul>
      <?php foreach ($nameServers as $nameServer) { ?>
        <li><?php echo $nameServer; ?></li>
      <?php } ?>
    </ul>
  <?php } else { ?>
    <p>No se pudo obtener información del ISP para la IP: <?php echo $domainIp; ?></p>
  <?php } ?>
</div>    
  </div>
</div>

<div class="container">
<div id="col-100">
<button onclick="window.history.back();">Verificar otra URL</button>
</div>
</div>

</div>
</body>
</html>
