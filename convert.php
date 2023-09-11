<?php

// Ruta del archivo de estado de la conversión
$stateFile = __DIR__ . '/conversion_complete.txt';

// Eliminar el archivo de estado existente si existe
if (file_exists($stateFile)) {
    unlink($stateFile);
}
// Ruta del directorio de salida (carpeta "images")
$outputDir = __DIR__ . '/images';

// Obtener la lista de imágenes en el directorio "images"
$imageFiles = glob($outputDir . '/*.jpg');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //echo 'Solicitud POST recibida.<br>';
    
    // Verificar si se ha subido un archivo y si es un PDF
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['type'] === 'application/pdf') {
        //echo 'Archivo PDF válido.<br>';
        
        // Ruta del archivo PDF
        $pdfFile = $_FILES['pdfFile']['tmp_name'];

        // Ruta del directorio de salida (carpeta "images")
        $outputDir = __DIR__ . '/images';

        // Eliminar todas las imágenes existentes en la carpeta "images"
        $existingImages = glob($outputDir . '/*.jpg');
        foreach ($existingImages as $image) {
            unlink($image);
        }

        // Nombre del archivo PDF original
        $pdfFileName = $_FILES['pdfFile']['name'];

        // Obtener la extensión del archivo PDF
        $pdfExtension = pathinfo($pdfFileName, PATHINFO_EXTENSION);

        // Crear el directorio de salida si no existe
        if (!file_exists($outputDir)) {
            mkdir($outputDir);
        }

        // Ruta del archivo convertido (imagen)
        $outputFile = $outputDir . '/' . $pdfFileName . '.jpg';

        // Carpeta de archivos PDF
        $pdfFolder = __DIR__ . '/archivos_pdf';

        // Crear la carpeta de archivos PDF si no existe
        if (!file_exists($pdfFolder)) {
            mkdir($pdfFolder);
        }

        // Ruta del archivo PDF en la carpeta de archivos PDF
        $pdfDestination = $pdfFolder . '/' . $pdfFileName;

        // Copiar el archivo PDF a la carpeta de archivos PDF
        if (move_uploaded_file($pdfFile, $pdfDestination)) {
            //echo 'El archivo PDF se copió correctamente.<br>';

            // Llamar al script Python solo si se copió correctamente el archivo PDF
            if (file_exists($pdfDestination)) {
                //echo 'Llamando al script Python.<br>';
                // Llamar al script Python
                $pythonScript = __DIR__ . '/convert.py';
                $command = 'python ' . escapeshellarg($pythonScript) . ' ' . escapeshellarg($pdfDestination) . ' ' . escapeshellarg($outputDir);

                // Ejecutar el comando Python
                exec($command);

                // Actualizar el archivo de estado
    file_put_contents(__DIR__ . '/conversion_complete.txt', 'Conversion completed');
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Imágenes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        h1 {
            text-align: center;
            background-color: #333;
            color: #fff;
            padding: 20px 0;
        }

        ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        li {
            margin: 10px;
            text-align: center;
        }

        img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        a {
            display: block;
            text-decoration: none;
            margin-top: 10px;
            background-color: #333;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #555;
        }

        input[type="submit"] {
        background-color: #007bff; /* Color de fondo azul brillante */
        color: #fff; /* Color de texto blanco */
        padding: 10px 20px; /* Espaciado interno */
        border: none; /* Sin borde */
        border-radius: 5px; /* Bordes redondeados */
        font-size: 16px; /* Tamaño del texto */
        cursor: pointer; /* Cambiar el cursor al pasar por encima */
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #0056b3; /* Color de fondo más oscuro al pasar el cursor */
    }

    </style>
</head>
<body>
    <div class="container">
    <h1>Lista de Imágenes Generadas</h1>

    <?php
    // Función para verificar si las conversiones han terminado
    function conversionsCompleted() {
        return file_exists(__DIR__ . '/conversion_complete.txt');
    }

    // Esperar hasta que las conversiones se completen
    while (!conversionsCompleted()) {
        // Esperar un segundo antes de verificar nuevamente
        sleep(1);
    }

    // Ruta del directorio de salida (carpeta "images")
    $lista_convertida = __DIR__ . '/images';

    // Obtener la lista de imágenes en el directorio "images"
    $imageFilesconvertida = glob($lista_convertida. '/*.jpg');

    // Coloca el botón de descarga del ZIP aquí, justo después del h1
    echo '<form action="zip_images.php" method="post">';
    echo '<input type="submit" name="download_zip" value="Descargar Todo.ZIP">';
    echo '</form>';

    if (!empty($imageFilesconvertida)) {
        echo '<ul>';
        $contador = 1; // Inicializar el contador
        foreach ($imageFilesconvertida as $imageFile1) {
            // Obtener el nombre del archivo de imagen
            $imageName = basename($imageFile1);
            echo '<li>';
            // Mostrar el número de enumeración junto a la imagen
            echo '<span>' . $contador . '. </span>';
            echo '<img src="images/' . $imageName . '" alt="' . $imageName . '"><br>';
            echo '<a href="images/' . $imageName . '" download="' . $imageName . '">Descargar ' . $imageName . '</a>';
            echo '</li>';
            $contador++; // Incrementar el contador
        }
        echo '</ul>';
    } else {
        echo 'No se han generado imágenes.';
    }
    ?>
</body>
</html>


