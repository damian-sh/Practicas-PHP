<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\TipoContactoRepository;

session_start();

$pdo = Database::obtenerConexion();
$tiposRepo = new TipoContactoRepository($pdo);
$tipos = $tiposRepo->obtenerTodos();

// Si venir desde un formulario que falló, se conservan los valores
$valores = [
    'nombre'  => (string) ($_SESSION['datos']['nombre'] ?? ''),
    'email'   => (string) ($_SESSION['datos']['email'] ?? ''),
    'telefono' => (string) ($_SESSION['datos']['telefono'] ?? ''),
    'tipo'    => (string) ($_SESSION['datos']['tipo'] ?? ''),
];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['datos'], $_SESSION['errores']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Contacto - Gestor de Contactos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5; padding: 20px; color: #2c3e50;
        }
        .container {
            max-width: 600px; margin: 0 auto; background-color: #fff;
            border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.1); padding: 30px;
        }
        h1 { border-bottom: 3px solid #3498db; padding-bottom: 10px; margin-bottom: 25px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: .9em; }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: 100%; padding: 11px; border: 1px solid #d5d8dc;
            border-radius: 4px; font-size: 1em; font-family: inherit;
        }
        input:focus, select:focus { outline: none; border-color: #3498db; }
        .campo { margin-bottom: 18px; }
        .ayuda { font-size: .8em; color: #7f8c8d; margin-top: 4px; }
        .error-campo { color: #c0392b; font-size: .82em; margin-top: 4px; }
        .alert {
            padding: 13px 15px; border-radius: 4px; margin-bottom: 18px;
            background-color: #fdecea; color: #922b21; border-left: 4px solid #c0392b;
        }
        .alert ul { margin-left: 20px; }
        .botones { display: flex; gap: 10px; margin-top: 25px; }
        .btn {
            display: inline-block; padding: 11px 22px; border: none; border-radius: 4px;
            font-size: .95em; font-weight: 600; cursor: pointer; text-decoration: none;
            font-family: inherit;
        }
        .btn-primario { background-color: #3498db; color: #fff; }
        .btn-primario:hover { background-color: #2980b9; }
        .btn-secundario { background-color: #95a5a6; color: #fff; }
        .btn-secundario:hover { background-color: #7f8c8d; }
    </style>
</head>
<body>
<div class="container">
    <h1>➕ Crear Nuevo Contacto</h1>

    <?php if (!empty($errores)): ?>
        <div class="alert">
            <strong>No se pudo guardar el contacto:</strong>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="procesar.php" novalidate>
        <input type="hidden" name="accion" value="crear">

        <div class="campo">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" maxlength="100"
                   value="<?= htmlspecialchars($valores['nombre']) ?>" required>
            <div class="ayuda">Obligatorio, máximo 100 caracteres.</div>
            <?php if (isset($errores['nombre'])): ?>
                <div class="error-campo"><?= htmlspecialchars($errores['nombre']) ?></div>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" maxlength="100"
                   value="<?= htmlspecialchars($valores['email']) ?>" required>
            <div class="ayuda">Obligatorio, debe tener formato válido y ser único.</div>
            <?php if (isset($errores['email'])): ?>
                <div class="error-campo"><?= htmlspecialchars($errores['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono *</label>
            <input type="tel" id="telefono" name="telefono" maxlength="20"
                   value="<?= htmlspecialchars($valores['telefono']) ?>" required>
            <div class="ayuda">Obligatorio, máximo 20 caracteres.</div>
            <?php if (isset($errores['telefono'])): ?>
                <div class="error-campo"><?= htmlspecialchars($errores['telefono']) ?></div>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="tipo">Tipo de Contacto *</label>
            <select id="tipo" name="tipo" required>
                <option value="">-- Seleccione un tipo --</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= (int) $tipo['id'] ?>"
                        <?= $valores['tipo'] === (string) $tipo['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $tipo['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['tipo'])): ?>
                <div class="error-campo"><?= htmlspecialchars($errores['tipo']) ?></div>
            <?php endif; ?>
        </div>

        <div class="botones">
            <button type="submit" class="btn btn-primario">Guardar</button>
            <a href="index.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
