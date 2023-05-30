# Herramienta de Comprobación y Verificación de Archivo ads.txt

El código proporcionado implementa una herramienta que permite comprobar y verificar el contenido del archivo ads.txt de un dominio específico. El archivo ads.txt es utilizado por los editores web para declarar a los compradores autorizados de su inventario publicitario.

El código consta de dos secciones HTML, una con JavaScript y otra con PHP, que interactúan para lograr la funcionalidad deseada. A continuación, se explica cada sección en detalle:

La sección HTML con JavaScript:

- En esta sección, hay un formulario con un campo de entrada de texto y un botón "Comprobar".
- Cuando se hace clic en el botón "Comprobar", se llama a la función `comprobarDominio()`.
- Dentro de esta función, se recupera el valor ingresado en el campo de texto y se construye una URL concatenando el dominio y "/ads.txt".
- Luego se crea una solicitud AJAX utilizando `XMLHttpRequest()` para obtener el contenido del archivo ads.txt de la URL.
- Cuando se completa la solicitud, si el estado y el código de respuesta son correctos (estado 4 y código 200), el contenido del archivo ads.txt se muestra en un campo de texto.

La sección HTML con PHP:

- En esta sección, se muestra un campo de texto de solo lectura que contiene el contenido del archivo ads.txt de un dominio específico.
- El dominio se recibe a través del parámetro GET `dominio`.
- Se obtiene el contenido del archivo ads.txt utilizando `file_get_contents()` y se guarda en un archivo temporal.
- Luego, el contenido del archivo temporal se muestra en el campo de texto.
- Además, se realiza una verificación del formato del archivo ads.txt línea por línea utilizando el código PHP.
- Si se encuentran errores de formato, se muestra un mensaje indicando la línea con el error.
- Al final, si no se encuentran errores, se muestra un mensaje indicando que no se encontraron errores en el archivo ads.txt.

## Requisitos:
Se requieren permisos de escritura (chmod) en la carpeta "tmp" para poder crear el archivo temporal.
El contenido del archivo temporal se muestra en el primer campo de texto.
