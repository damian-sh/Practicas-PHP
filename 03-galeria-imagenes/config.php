<?php

declare(strict_types=1);

/**
 * config.php
 * Configuración de la aplicación: autoloader, rutas y límites.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carpeta donde se guardan las imágenes
define('DIRECTORIO_UPLOADS', __DIR__ . '/uploads/');

// Tamaño máximo: 5 MB
define('TAMANIO_MAXIMO', 5 * 1024 * 1024);

// Tipos permitidos
define('TIPOS_PERMITIDOS', ['image/jpeg', 'image/png', 'image/gif']);
define('EXTENSIONES_PERMITIDAS', ['jpg', 'jpeg', 'png', 'gif']);

// Tamaño de la miniatura
define('ANCHO_MINIATURA', 300);
define('ALTO_MINIATURA', 300);

// Calidad al guardar
define('CALIDAD_IMAGEN', 90);

// Crear directorio si no existe
if (!is_dir(DIRECTORIO_UPLOADS)) {
    mkdir(DIRECTORIO_UPLOADS, 0755, true);
}

// Zona horaria
date_default_timezone_set('America/El_Salvador');
