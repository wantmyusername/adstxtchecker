<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" sizes="192x192" href="https://www.gstatic.com/mobilesdk/160503_mobilesdk/logo/2x/firebase_96dp.png">
    <title>Probador de Ads.txt para Google AdSense</title>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
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
            border: 1px solid #dadce0;
            border-radius: 5px;
            font-size: 11.1px;
            font-family: monospace;
        }

        textarea.numbered {
            background: url(http://i.imgur.com/2cOaJ.png);
            background-attachment: local;
            background-repeat: no-repeat;
            padding-left: 35px;
            padding-top: 10px;
            border-color: #ccc;
        }

        #estado {
            padding: 10px;
            border: 1px solid #ccc;
        }

        #resultado {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .alert__red {
            border: 1px solid #ed7f80;
            border-radius: 3px;
            background: #fdf6f5;
            padding: 9px;
            font-size: 14px;
        }

        .br-2 {
            border-radius: 3px;
        }

        .article-meta__abstract {
            align-items: center !important;
        }

        .h3-head {
            font-size: 33px;
        }

        .border-l-4 {
            border-left: 4px solid !important;
        }

        .p-4 {
            padding: 1rem !important;
        }

        p.h-c-eyebrow.article-hero__primary-tag--link__text {
            font-size: 18px;
        }

        .border-green-500 {
            --border-opacity: 1 !important;
            border-color: #48bb78 !important;
            border-color: rgba(72, 187, 120, var(--border-opacity)) !important;
        }

        .bg-teal-100 {
            --bg-opacity: 1 !important;
            background-color: #e6fffa !important;
            background-color: rgba(230, 255, 250, var(--bg-opacity)) !important;
        }

        .text-green-700 {
            --text-opacity: 1 !important;
            color: #2f855a !important;
            color: rgba(47, 133, 90, var(--text-opacity)) !important;
        }

        .border-l-4 {
            border-left: 4px solid !important;
        }

        .border-yellow-500 {
            --border-opacity: 1 !important;
            border-color: #c05621 !important;
        }

        .bg-yellow-100 {
            background-color: #fffaf1 !important;
        }

        .text-yellow-700 {
            --text-opacity: 1 !important;
            color: #c05621 !important;
        }

        .p-4 {
            padding: 1rem !important;
        }

        .border-red-500 {
            --border-opacity: 1 !important;
            border-color: #f56565 !important;
            border-color: rgba(245, 101, 101, var(--border-opacity)) !important;
        }

        .bg-red-100 {
            background-color: #fff5f5 !important;
        }

        .text-red-700 {
            --text-opacity: 1 !important;
            color: #c53030 !important;
            color: rgba(197, 48, 48, var(--text-opacity)) !important;
        }

        .status_css {
            font-size: 15px;
        }

        .container.datos__adstxt {
            border-top: 2px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
            padding: 15px 0px 15px 0px;
        }

        .newsletter-form__submit {
            display: block;
            margin: auto;
            width: 50%;
        }

        .hr {
            border: 1px solid #e2e8f0;
            margin: 15px 0px 15px 0px;
        }

        .article-hero {
            padding: 25px 16px 0px;
        }

        @media (min-width: 1024px) .article-hero {
            padding: 37px 32px 0px;
        }
    </style>
    <style>
        @media (max-width: 600px) {
            #col-50 {
                width: 100%;
            }
        }

        @media (min-width: 1024px) .article-hero {
            padding: 37px 32px 30px;
        }

        .minitexto {
            font-size: 14px;
            color: #191515b5;
        }

        .recomendacion {
            color: #0000009c;
            font-size: 15px !important;
        }

        .reco {
            list-style: circle;
            font-family: monospace;
        }
    </style>
</head>

<body>
<?php
$dominio = $_GET['dominio'];

$url = 'http://' . $dominio;
$urlParsed = parse_url($url);

if ($urlParsed === false || !isset($urlParsed['host'])) {
  echo "Verifica la URL o prueba nuevamente.";
  exit;
}

// Obtener el dominio
$dominio = $urlParsed['host'];

// Verificar si hay algo después del dominio
if (isset($urlParsed['path']) || isset($urlParsed['query']) || isset($urlParsed['fragment'])) {
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

    <main id="jump-content" class="site-content" tabindex="-1">
        <article class="uni-article-wrapper">
            <section class="article-hero">
                <div class="article-hero__container">
                    <div class="article-hero__primary-tag__col">
                        <p class="h-c-eyebrow article-hero__primary-tag--link__text">Resultados</p>
                    </div>
                    <h1 class="article-hero__h1">Herramienta de visualización de Ads.txt</h1>
                </div>
            </section>

            <section class="uni-container article-container">
                <div class="uni-wrapper article-container__wrapper">
                    <div class="uni-content uni-blog-article-container article-container__content"
                        data-reading-time="true" data-component="uni-drop-cap|uni-tombstone">

                        <!--article text-->

                        <div class="module--text module--text__article">

                            <div class="container hero">
                                <div id="col-100">
                                    <h5>Verificación del dominio</h5>
                                    <h3>
                                        <?php echo $dominio ?>
                                    </h3>
                                </div>
                            </div>

                            <div class="container contenido__adstxt">
                                <div id="col-100">
                                    <p class="h-c-eyebrow article-hero__primary-tag--link__text">Contenido del archivo
                                        ads.txt</p>
                                    <div class="numbered-textarea">
                                        <textarea id="contenidoAds" class="numbered" rows="10" cols="50" readonly><?php echo $tempFileContent; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="container datos__adstxt">
                                <div id="col-100">
                                    <p class="h-c-eyebrow article-hero__primary-tag--link__text">Estado del archivo</p>
                                    <div class="status_css">
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
                                              echo "
                                              <div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'>
                                                <b>⚠ Ups!</b>
                                                <p> El archivo ads.txt está vacío o no existe</p>
                                              </div>   
                                             ";
                                              $errors = true;
                                            } elseif (!$errors) {
                                              echo "
                                              <div class='bg-teal-100 border-l-4 border-green-500 text-green-700 p-4' role='alert'>
                                        <b>✅  ¡Muy bien!</b>
                                            <p> No se encontraron errores en el archivo ads.txt.<br/>El archivo es accesible por AdSense</p>
                                            <p class='minitexto'>La notificación de Ads.txt toma de 7 a 14 días en desaparecer de tu cuenta de AdSense. No es en automático.</p>
                                        </div> ";}
                                        ?>


                                    </div>
                                </div>
                            </div>

                            <div class="container">
                                <div id="col-50">
                                    <p class="h-c-eyebrow article-hero__primary-tag--link__text">Datos técnicos</p>
                                    <div id="estado__datos_tecnicos">
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
                                                        echo '
                                                        <div class="bg-teal-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                                                            <b>✅  ¡Muy bien!</b>
                                                            <p>El código de AdSense está correctamente instalado</p>
                                                        </div>';
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            if (!$encontrado) {
                                                echo '
                                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                                                <b>¡CUIDADO!</b>
                                                <p>El código de AdSense no está instalado en este sitio</p>
                                                <p class="minitexto">Si instalaste SiteKit y el código de forma manual, debes de usar sólo uno.</p>
                                                <p class="minitexto">Si es tu primera vez, se recomienda instalar de forma manual, una vez que te aprueben el sitio, quita el código manual y cambia a SiteKit.</p>
                                                </div>';
                                            }
                                        ?>

                                        <hr>
                                        <p>La IP del dominio '
                                            <?php echo $dominio; ?>' es:
                                            <?php echo $domainIp; ?>
                                        </p>
                                        <hr>
                                        <?php if ($ispInfo && $ispInfo->status === "success") { ?>
                                        <?php if ($isCloudflare || preg_match('/\.\w+\.cloudflare\.com$/', $domainIp) || preg_match('/\.\w+\.cloudflare\.com$/', implode(',', $nameServers))) { ?>

                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                                            <b>Advertencia:</b>
                                            <p>El ISP o los servidores de nombres detectados pertenecen a Cloudflare.
                                                Ten en cuenta que Cloudflare puede afectar la disponibilidad y el
                                                contenido del archivo ads.txt.</p>
                                            <p class="minitexto"><a href="https://community.cloudflare.com/t/how-to-set-up-ads-txt-file-in-cloudflare/123456/8" target="_blank">Ver como solucionarlo ↗</a></p>
                                        </div>
                                        <?php } else { ?>
                                        <!-- <p>No se detectó Cloudflare como el ISP o los servidores de nombres.</p> -->
                                        <?php } ?>
                                        <hr>
                                        <p class="h-c-eyebrow article-hero__primary-tag--link__text">Información
                                            adicional</p>
                                        <p><b>ISP:</b>
                                            <?php echo $ispInfo->isp; ?><br>
                                            <b>City:</b>
                                            <?php echo $ispInfo->city; ?><br>
                                            <b>Country:</b>
                                            <?php echo $ispInfo->country; ?>
                                        </p>
                                        <p><b>Servidores de nombres (Name Servers):</b></p>
                                        <ul>
                                            <?php foreach ($nameServers as $nameServer) { ?>
                                            <li>
                                                <?php echo $nameServer; ?>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <?php } else { ?>
                                        <p>No se pudo obtener información del ISP para la IP:
                                            <?php echo $domainIp; ?>
                                        </p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div id="col-50">
                                    <p class="h-c-eyebrow article-hero__primary-tag--link__text">Rastreabilidad del
                                        sitio</p>
                                    <div id="estado_rastreabilidad">
                                        <?php
                                        // URL del sitio web
                                        $siteUrl = $url_f;

                                        // URL del archivo robots.txt
                                        $robotsTxtUrl = $siteUrl . "/robots.txt";

                                        // Verificar si el archivo robots.txt existe
                                        if (!url_exists($robotsTxtUrl)) {
                                            echo "
                                            <div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'>
                                                <b>Hey!</b>
                                                <p>El archivo robots.txt no existe.</p>
                                            </div>
                                            ";
                                        } else {

                                            // Obtener el contenido del archivo robots.txt
                                            $robotsTxtContent = file_get_contents($robotsTxtUrl);

                                            // Variables de control
                                            $canCrawl = true;
                                            $blockedSections = [];

                                            // Parsear el contenido del archivo robots.txt
                                            $lines = explode("\n", $robotsTxtContent);
                                            $userAgent = "";
                                            foreach ($lines as $line) {
                                                $line = trim($line);

                                                // Ignorar líneas con errores de sintaxis
                                                if (
                                                    preg_match("/^(User-agent:|Disallow:|Allow:|Sitemap:)/i", $line) !== 1
                                                ) {
                                                    continue;
                                                }

                                                // Verificar bloqueo de secciones
                                                if (strncasecmp($line, "User-agent:", 11) === 0) {
                                                    $userAgent = trim(substr($line, 11));
                                                } elseif (
                                                    strcasecmp($userAgent, "Googlebot") === 0 ||
                                                    strcasecmp($userAgent, "Mediapartners-Google") === 0
                                                ) {
                                                    if (strncasecmp($line, "Disallow:", 9) === 0) {
                                                        $section = trim(substr($line, 9));
                                                        if ($section !== "") {
                                                            $blockedSections[] = $section;
                                                        }
                                                    }
                                                } elseif (strcasecmp($userAgent, "*") === 0) {
                                                    if (strncasecmp($line, "Disallow:", 9) === 0) {
                                                        $section = trim(substr($line, 9));
                                                        if ($section !== "") {
                                                            $blockedSections[] = $section;
                                                        }
                                                    }
                                                }

                                                // Verificar bloqueo total del sitio
                                                if (
                                                    strcasecmp($line, "User-agent: *") === 0 &&
                                                    strcasecmp(trim(next($lines)), "Disallow: /") === 0
                                                ) {
                                                    $canCrawl = false;
                                                    break;
                                                }
                                            }

                                            // Mostrar el resultado
                                            if (!empty($blockedSections)) {
                                                echo "
                                                <div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4' role='alert'>
                                                    <b>Info!</b>
                                                    <p>El sitio es accesible por Google pero cuenta con varias secciones bloqueadas en el archivo <a href='$url_f/robots.txt' target='target_blank'>Robots.txt</a>:</p>
                                                    <ul>";
                                                foreach ($blockedSections as $section) {
                                                    echo "<li class='section_robots'>$section</li>";
                                                }
                                                echo "
                                                    </ul>
                                                </div>
                                                ";
                                            } elseif (!$canCrawl) {
                                                echo "
                                                <div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'>
                                                    <b>Alerta!</b>
                                                    <p>El sitio está bloqueando Google y otros buscadores. Verifica tu archivo <a href='$url_f/robots.txt' target='target_blank'>Robots.txt</a></p>
                                                </div>
                                                ";
                                            } else {
                                                echo "
                                                <div class='bg-teal-100 border-l-4 border-green-500 text-green-700 p-4' role='alert'>
                                                    <b>Genial!</b>
                                                    <p>Tu sitio puede ser rastreado por Google y otros buscadores.</p>
                                                </div>
                                                ";
                                            }
                                        }

                                        // Función para verificar si una URL existe
                                        function url_exists($url)
                                        {
                                            $file_headers = @get_headers($url);
                                            return !(!$file_headers || $file_headers[0] == "HTTP/1.1 404 Not Found");
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div id="col-100">
                                    <hr>
                                    <p class="h-c-eyebrow article-hero__primary-tag--link__text">Recomendación</p><br/>
                                    <p>Asegúrate de que cada detalle en tu sitio sea visible. Accede a tu página
                                        utilizando las siguientes variantes de URL:</p>
                                    <ul class="reco">
                                        <li>http://</li>
                                        <li>http://www.</li>
                                        <li>https://</li>
                                        <li>https://www.</li>
                                    </ul>
                                    <p>Cada versión debe redirigir a una versión específica. Además, verifica que tu
                                        archivo ads.txt sea visible en todas las secciones, en caso de que no lo esté.
                                    </p>
                                    <p>Ten en cuenta que la versión con "www" no se aplica a los subdominios.</p>
                                </div>
                            </div>
                            <div class="container">
                                <div id="col-100">
                                    <hr>
                                </div>
                            </div>
                            <div class="container">
                                <div id="col-100">
                                    <button onclick="window.history.back();" class="kw-button kw-button--high-emphasis newsletter-form__submit" aria-label="Subscribe" data-content-type="blogv2 | newsletter page" data-page="in-line">Verificar otra URL</button>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <ul>
                            <li><a href="https://adstxt-generator-9770a.web.app/" target="_blank">Generador de archivo ads.txt</a></li>
                            <li><a href="https://support.google.com/adsense/answer/7532444?hl=es" target="_blank">Guía Ads.txt</a>
                    </div>
                </div>
                </div>
            </section>
        </article>
    </main>
    <footer class="h-c-footer h-c-footer--topmargin h-c-footer--standard h-has-social" id="footer-standard">
        <form action="en.html" method="get">
            <section class="h-c-footer__upper">
                <section class="h-c-social">
                    <p class="newsletter-form__info-paragraph">
                        Esta herramienta es independiente y no está asociada directa o indirectamente con Google. Fue
                        creada por usuarios para usuarios no se almacena ningún dato en absoluto, como lo son cookies,
                        visitas o identificadores de cuentas introducidas. Esta herramienta está diseñada para facilitar
                        la verificación del archivo ads.txt. Todas las marcas mencionadas pertenecen a sus respectivos
                        propietarios.
                    </p>
                </section>
            </section>
            <section class="h-c-footer__global">
                <ul class="h-c-footer__global-links h-c-footer__global-links--extra h-no-bullet">
                    <div class="uni-picker" data-component="uni-lang-picker">
                        <select name="language" class="uni-picker__order-menu" id="language" onchange="this.form.submit()">
                <option class="uni-picker__item" selected="selected">
                  Español (Latinoamérica)
                </option>
                <option class="uni-picker__item">
                  English
                </option>
              </select>
                    </div>
                    </li>
                </ul>
            </section>
        </form>
    </footer>
</body>
</html>
