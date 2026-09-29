<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\ContactoRepository;
use App\Database;

session_start();

$pdo = Database::obtenerConexion();
$contactosRepo = new ContactoRepository($pdo);

$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

/* Verificar que el contacto exista antes de eliminar */
$contacto = $id > 0 ? $contactosRepo->obtenerPorId($id) : null;

if ($contacto === null) {
    $_SESSION['error'] = 'El contacto solicitado no existe.';
    header('Location: index.php');
    exit;
}

/* ----------------- Confirmación (GET) / Eliminación (POST) ----------------- */

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
        $contactosRepo->eliminar($id);
        $_SESSION['exito'] = 'Contacto eliminado correctamente.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'No se pudo eliminar el contacto: ' . $e->getMessage();
    }

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Eliminación - Gestor de Contactos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5; padding: 20px; color: #2c3e50;
        }
        .container {
            max-width: 520px; margin: 0 auto; background-color: #fff;
            border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.1); padding: 30px;
        }
        h1 { border-bottom: 3px solid #e74c3c; padding-bottom: 10px; margin-bottom: 20px; }
        .advertencia {
            background-color: #fdecea; color: #922b21; padding: 15px;
            border-radius: 4px; border-left: 4px solid #c0392b; margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #ecf0f1; text-align: left; }
        th { background-color: #f8f9fa; width: 40%; font-weight: 600; }
        .botones { display: flex; gap: 10px; }
        .btn {
            display: inline-block; padding: 11px 22px; border: none; border-radius: 4px;
            font-size: .95em; font-weight: 600; cursor: pointer; text-decoration: none;
            font-family: inherit;
        }
        .btn-peligro { background-color: #e74c3c; color: #fff; }
        .btn-peligro:hover { background-color: #c0392b; }
        .btn-secundario { background-color: #95a5a6; color: #fff; }
        .btn-secundario:hover { background-color: #7f8c8d; }
    </style>
</head>
<body>
<div class="container">
    <h1>🗑️ Confirmar Eliminación</h1>

    <div class="advertencia">
        <strong>¿Está seguro que desea eliminar este contacto?</strong><br>
        Esta acción no se puede deshacer.
    </div>

    <table>
        <tr><th>ID</th><td><?= (int) $contacto['id'] ?></td></tr>
        <tr><th>Nombre</th><td><?= htmlspecialchars((string) $contacto['nombre']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars((string) $contacto['email']) ?></td></tr>
        <tr><th>Teléfono</th><td><?= htmlspecialchars((string) $contacto['telefono']) ?></td></tr>
        <tr><th>Tipo</th><td><?= htmlspecialchars((string) $contacto['tipo_nombre']) ?></td></tr>
    </table>

    <form method="POST" action="eliminar.php">
        <input type="hidden" name="id" value="<?= (int) $contacto['id'] ?>">
        <div class="botones">
            <button type="submit" class="btn btn-peligro">Sí, eliminar</button>
            <a href="index.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
