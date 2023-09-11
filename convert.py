from pdf2image import convert_from_path
import sys
import os

# Ruta del archivo PDF (pasada como argumento)
pdf_path = sys.argv[1]

# Obtener el nombre del archivo sin extensión
filename = os.path.splitext(os.path.basename(pdf_path))[0]

# Obtener la ruta al directorio bin de Poppler
poppler_path = os.path.join(os.getcwd(), 'poppler-0.68.0', 'bin')

# Ruta de la carpeta para guardar las imágenes generadas
output_folder = sys.argv[2]

try:
    # Convertir el PDF a imágenes (formato JPEG)
    images = convert_from_path(pdf_path, poppler_path=poppler_path)

    # Guardar las imágenes en archivos JPEG con el mismo nombre del PDF
    for i, image in enumerate(images):
        image_path = os.path.join(output_folder, f'{filename}_{i}.jpg')
        image.save(image_path, 'JPEG')

    print("La conversión a imágenes se ha completado exitosamente.")
except Exception as e:
    print(f"Error al convertir el PDF a imágenes: {str(e)}")