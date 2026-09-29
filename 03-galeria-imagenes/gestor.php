<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * gestor.php
 * Clase GestorImagenes: valida, guarda, redimensiona, lista y elimina imágenes.
 */
class GestorImagenes
{
    /**
     * Valida que la imagen cumpla los requisitos.
     */
    public function validarImagen($archivo)
    {
        // Verificar que el archivo exista
        if (!isset($_FILES[$archivo])) {
            return ['exito' => false, 'mensaje' => 'No se enviaron archivos'];
        }

        $archivo_data = $_FILES[$archivo];

        // Validar errores de carga
        if ($archivo_data['error'] !== UPLOAD_ERR_OK) {
            $mensajes = [
                UPLOAD_ERR_INI_SIZE  => 'Archivo excede tamaño máximo del servidor',
                UPLOAD_ERR_FORM_SIZE => 'Archivo excede tamaño máximo del formulario',
                UPLOAD_ERR_PARTIAL   => 'Archivo fue cargado parcialmente',
                UPLOAD_ERR_NO_FILE   => 'No se cargó ningún archivo',
            ];

            return [
                'exito'   => false,
                'mensaje' => $mensajes[$archivo_data['error']] ?? 'Error desconocido',
            ];
        }

        // Validar tamaño
        if ($archivo_data['size'] > TAMANIO_MAXIMO) {
            return ['exito' => false, 'mensaje' => 'Archivo demasiado grande (máximo 5MB)'];
        }

        // Validar tipo MIME
        if (!in_array($archivo_data['type'], TIPOS_PERMITIDOS)) {
            return ['exito' => false, 'mensaje' => 'Tipo de archivo no permitido. Solo JPEG, PNG, GIF'];
        }

        // Validar extensión
        $extension = strtolower(pathinfo($archivo_data['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, EXTENSIONES_PERMITIDAS)) {
            return ['exito' => false, 'mensaje' => 'Extensión no permitida'];
        }

        return ['exito' => true, 'archivo' => $archivo_data];
    }

    /**
     * Guardar imagen en el servidor
     */
    public function guardarImagen($archivo)
    {
        // Generar nombre único y seguro
        $nombreArchivo = uniqid() . '_' .
            preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($archivo['name']));

        $ruta = DIRECTORIO_UPLOADS . $nombreArchivo;

        // Mover archivo de temporal a la carpeta de uploads
        if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
            return ['exito' => false, 'mensaje' => 'Error al guardar el archivo'];
        }

        return ['exito' => true, 'ruta' => $ruta];
    }

    /**
     * Redimensionar imagen a miniatura
     */
    public function redimensionarImagen($ruta)
    {
        try {
            $gestor = new ImageManager(new Driver());

            $gestor->decodePath($ruta)
                ->resizeDown(ANCHO_MINIATURA, ALTO_MINIATURA)
                ->save($ruta, quality: CALIDAD_IMAGEN);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Obtener lista de todas las imágenes
     */
    public function obtenerImagenes()
    {
        $imagenes = [];

        if (!is_dir(DIRECTORIO_UPLOADS)) {
            return $imagenes;
        }

        $archivos = scandir(DIRECTORIO_UPLOADS);

        foreach ($archivos as $archivo) {
            // Saltar carpetas especiales
            if ($archivo === '.' || $archivo === '..') {
                continue;
            }

            $ruta_completa = DIRECTORIO_UPLOADS . $archivo;

            // Verificar que sea un archivo de imagen
            if (!is_file($ruta_completa)) {
                continue;
            }

            $extension = strtolower((string) pathinfo($archivo, PATHINFO_EXTENSION));

            if (!in_array($extension, EXTENSIONES_PERMITIDAS)) {
                continue;
            }

            $imagenes[] = [
                'archivo' => $archivo,
                'ruta'    => 'uploads/' . $archivo,
                'tamanio' => filesize($ruta_completa),
                'fecha'   => date('d/m/Y H:i:s', filemtime($ruta_completa)),
            ];
        }

        // Ordenar por fecha descendente (más recientes primero)
        usort($imagenes, function ($a, $b) {
            return filemtime(DIRECTORIO_UPLOADS . $a['archivo'])
                <=> filemtime(DIRECTORIO_UPLOADS . $b['archivo']);
        });

        return array_reverse($imagenes);
    }

    /**
     * Eliminar una imagen
     */
    public function eliminarImagen($archivo)
    {
        $archivo = basename($archivo); // Seguridad: evitar path traversal
        $ruta = DIRECTORIO_UPLOADS . $archivo;

        if (file_exists($ruta) && is_file($ruta)) {
            return unlink($ruta);
        }

        return false;
    }
}
