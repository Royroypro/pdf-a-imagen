<?php
// Verificar si se ha hecho clic en el botón de descarga del archivo ZIP
if (isset($_POST['download_zip'])) {
    // Ruta del directorio de salida (carpeta "images")
    $lista_convertida = __DIR__ . '/images';

    // Obtener la lista de imágenes en el directorio "images"
    $imageFilesconvertida = glob($lista_convertida. '/*.jpg');

    if (!empty($imageFilesconvertida)) {
        // Crear un archivo ZIP
        $zip = new ZipArchive();
        $zipFileName = 'Todas_las_imagenes.zip';

        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($imageFilesconvertida as $imageFile) {
                // Agregar cada imagen al archivo ZIP
                $imageName = basename($imageFile);
                $zip->addFile($imageFile, $imageName);
            }

            $zip->close();

            // Descargar el archivo ZIP
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zipFileName);
            header('Content-Length: ' . filesize($zipFileName));
            readfile($zipFileName);

            // Eliminar el archivo ZIP después de la descarga
            unlink($zipFileName);
        } else {
            echo 'Error al crear el archivo ZIP.';
        }
    } else {
        echo 'No hay imágenes para comprimir.';
    }
} else {
    // Redirigir de nuevo a la página principal si se accede a esta página directamente
    header('Location: index.html');
}
?>
