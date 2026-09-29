<?php

declare(strict_types=1);

require_once __DIR__ . '/gestor.php';

$gestor = new GestorImagenes();

$mensaje = '';
$tipo_mensaje = '';

/* ------------------------- Procesar formulario ------------------------- */

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {

    // Acción: eliminar una imagen
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $archivo = (string) ($_POST['archivo'] ?? '');

        if ($gestor->eliminarImagen($archivo)) {
            $mensaje = '🗑️ Imagen eliminada correctamente.';
            $tipo_mensaje = 'exito';
        } else {
            $mensaje = '✗ Error: no se pudo eliminar la imagen.';
            $tipo_mensaje = 'error';
        }
    }
    // Acción: subir una imagen
    elseif (isset($_FILES['imagen'])) {
        $validacion = $gestor->validarImagen('imagen');

        if (!$validacion['exito']) {
            $mensaje = '✗ ' . $validacion['mensaje'];
            $tipo_mensaje = 'error';
        } else {
            $guardado = $gestor->guardarImagen($validacion['archivo']);

            if (!$guardado['exito']) {
                $mensaje = '✗ ' . $guardado['mensaje'];
                $tipo_mensaje = 'error';
            } elseif ($gestor->redimensionarImagen($guardado['ruta'])) {
                $mensaje = '✓ Imagen subida y procesada correctamente.';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = '⚠ La imagen se subió, pero no se pudo redimensionar.';
                $tipo_mensaje = 'advertencia';
            }
        }
    }
}

/* ----------------------------- Galería ----------------------------- */

$imagenes = $gestor->obtenerImagenes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Imágenes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="contenedor">
        <header>
            <h1>🖼️ Galería de Imágenes</h1>
            <p class="subtitulo">Sube, organiza y gestiona tus imágenes</p>
        </header>

        <?php if ($mensaje !== ''): ?>
            <div class="mensaje <?= htmlspecialchars($tipo_mensaje) ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de carga -->
        <section class="seccion-formulario">
            <form method="POST" enctype="multipart/form-data" class="formulario">
                <div class="grupo-archivo">
                    <label for="imagen" class="etiqueta-archivo">Selecciona una imagen</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/gif"
                           required class="entrada-archivo">
                    <p class="ayuda">Formatos: JPEG, PNG, GIF &nbsp;|&nbsp; Máximo: 5 MB</p>
                </div>
                <button type="submit" class="boton-subir">Subir Imagen</button>
            </form>
        </section>

        <!-- Galería de imágenes -->
        <section class="seccion-galeria">
            <h2>Imágenes Subidas (<?= count($imagenes) ?>)</h2>

            <?php if (empty($imagenes)): ?>
                <div class="sin-imagenes">
                    <p>No hay imágenes todavía. ¡Sube la primera!</p>
                </div>
            <?php else: ?>
                <div class="galeria">
                    <?php foreach ($imagenes as $imagen): ?>
                        <div class="imagen-item">
                            <div class="imagen-contenedor">
                                <img src="<?= htmlspecialchars($imagen['ruta']) ?>"
                                     alt="<?= htmlspecialchars($imagen['archivo']) ?>"
                                     loading="lazy">
                            </div>

                            <div class="imagen-info">
                                <p class="nombre" title="<?= htmlspecialchars($imagen['archivo']) ?>">
                                    <?= htmlspecialchars($imagen['archivo']) ?>
                                </p>
                                <p class="detalles">
                                    <?= number_format($imagen['tamanio'] / 1024, 2) ?> KB &nbsp;|&nbsp;
                                    <?= htmlspecialchars($imagen['fecha']) ?>
                                </p>
                            </div>

                            <form method="POST" class="formulario-eliminar">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="archivo"
                                       value="<?= htmlspecialchars($imagen['archivo']) ?>">
                                <button type="submit" class="boton-eliminar"
                                        onclick="return confirm('¿Eliminar esta imagen?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <footer>
            <p>Galería de Imágenes v1.0 — Miniatura <?= ANCHO_MINIATURA ?>×<?= ALTO_MINIATURA ?>px</p>
        </footer>
    </div>
</body>
</html>
