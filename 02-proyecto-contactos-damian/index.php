<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\ContactoRepository;
use App\Database;
use App\TipoContactoRepository;

session_start();

$pdo = Database::obtenerConexion();
$contactosRepo = new ContactoRepository($pdo);
$tiposRepo = new TipoContactoRepository($pdo);

// Filtro por tipo de contacto (0 = todos)
$tipoSeleccionado = isset($_GET['tipo']) && $_GET['tipo'] !== ''
    ? (int) $_GET['tipo']
    : 0;

$tipos = $tiposRepo->obtenerTodos();
$contactos = $contactosRepo->obtenerTodos($tipoSeleccionado > 0 ? $tipoSeleccionado : null);
$totalContactos = $contactosRepo->contarTodos();

// Mensajes de éxito / error dejados por procesar.php o eliminar.php
$exito = $_SESSION['exito'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['exito'], $_SESSION['error']);

$nombreTipo = 'Todos los contactos';
foreach ($tipos as $tipo) {
    if ((int) $tipo['id'] === $tipoSeleccionado) {
        $nombreTipo = (string) $tipo['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Contactos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5; padding: 20px; color: #2c3e50;
        }
        .container {
            max-width: 1000px; margin: 0 auto; background-color: #fff;
            border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.1); padding: 30px;
        }
        h1 { border-bottom: 3px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { margin: 25px 0 12px; font-size: 1.25em; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #3498db; color: #fff; padding: 12px; text-align: left; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #ecf0f1; }
        tr:hover { background-color: #f8f9fa; }
        .empty-message {
            background-color: #fff3cd; color: #856404; padding: 15px;
            border-radius: 4px; border-left: 4px solid #ffc107;
        }
        .alert { padding: 13px 15px; border-radius: 4px; margin-bottom: 18px; border-left: 4px solid; }
        .alert-exito { background-color: #eafaf1; color: #1e8449; border-color: #27ae60; }
        .alert-error { background-color: #fdecea; color: #922b21; border-color: #c0392b; }
        .barra {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px; margin-bottom: 10px;
        }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: .9em; }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: 100%; padding: 11px; border: 1px solid #d5d8dc;
            border-radius: 4px; font-size: 1em; font-family: inherit;
        }
        input:focus, select:focus { outline: none; border-color: #3498db; }
        .filtro { display: flex; align-items: flex-end; gap: 10px; }
        .filtro div { min-width: 220px; }
        .btn {
            display: inline-block; padding: 11px 18px; border: none; border-radius: 4px;
            font-size: .95em; font-weight: 600; cursor: pointer; text-decoration: none;
            font-family: inherit;
        }
        .btn-primario { background-color: #3498db; color: #fff; }
        .btn-primario:hover { background-color: #2980b9; }
        .btn-secundario { background-color: #95a5a6; color: #fff; }
        .btn-secundario:hover { background-color: #7f8c8d; }
        .btn-editar { background-color: #f39c12; color: #fff; padding: 7px 13px; font-size: .85em; }
        .btn-editar:hover { background-color: #d68910; }
        .btn-eliminar { background-color: #e74c3c; color: #fff; padding: 7px 13px; font-size: .85em; }
        .btn-eliminar:hover { background-color: #c0392b; }
        .acciones { display: flex; gap: 6px; }
        .contador {
            background-color: #ebf5fb; color: #2471a3; padding: 10px 14px;
            border-radius: 4px; display: inline-block; margin-top: 8px; font-size: .9em;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📇 Gestor de Contactos</h1>

    <?php if ($exito !== null): ?>
        <div class="alert alert-exito"><?= htmlspecialchars($exito) ?></div>
    <?php endif; ?>

    <?php if ($error !== null): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="barra">
        <form method="GET" action="index.php" class="filtro">
            <div>
                <label for="tipo">Filtrar por tipo de contacto</label>
                <select name="tipo" id="tipo" onchange="this.form.submit()">
                    <option value="0" <?= $tipoSeleccionado === 0 ? 'selected' : '' ?>>
                        Todos los contactos
                    </option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= (int) $tipo['id'] ?>"
                            <?= $tipoSeleccionado === (int) $tipo['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $tipo['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <noscript><button type="submit" class="btn btn-primario">Filtrar</button></noscript>
        </form>

        <a href="crear.php" class="btn btn-primario">+ Nuevo Contacto</a>
    </div>

    <h2>Listado de Contactos</h2>
    <span class="contador">
        Mostrando <strong><?= count($contactos) ?></strong> de
        <strong><?= $totalContactos ?></strong> contacto(s) — Filtro: <?= htmlspecialchars($nombreTipo) ?>
    </span>

    <?php if (empty($contactos)): ?>
        <div class="empty-message" style="margin-top:15px;">
            No hay contactos registrados<?= $tipoSeleccionado > 0 ? ' para este tipo de contacto' : '' ?>.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tipo de Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contactos as $contacto): ?>
                    <tr>
                        <td><?= (int) $contacto['id'] ?></td>
                        <td><?= htmlspecialchars((string) $contacto['nombre']) ?></td>
                        <td><?= htmlspecialchars((string) $contacto['email']) ?></td>
                        <td><?= htmlspecialchars((string) $contacto['telefono']) ?></td>
                        <td><?= htmlspecialchars((string) $contacto['tipo_nombre']) ?></td>
                        <td>
                            <div class="acciones">
                                <a href="editar.php?id=<?= (int) $contacto['id'] ?>"
                                   class="btn btn-editar">Editar</a>
                                <a href="eliminar.php?id=<?= (int) $contacto['id'] ?>"
                                   class="btn btn-eliminar">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
