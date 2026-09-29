<?php

declare(strict_types=1);

// public/index.php
// [CONCEPTO] Definir ruta base del proyecto
define('BASE_PATH', dirname(__DIR__));

// [CONCEPTO] Cargar el autoloader de Composer
require_once BASE_PATH . '/vendor/autoload.php';

// [CONCEPTO] Importar las clases que necesitas
use App\Contracts\Reservable;
use App\Domain\Horario;
use App\Domain\Reserva;
use App\Services\ReservaStorageService;

// [CONCEPTO] Inyección de dependencias: instanciar el Storage
$storage = new ReservaStorageService();
$rutaJson = BASE_PATH . '/data/reservas.json';

// [CONCEPTO] Obtener datos del archivo JSON (persistencia)
$hayArchivo = file_exists($rutaJson);
$datos = $hayArchivo ? $storage->leerDeJson($rutaJson) : [];

// [CONCEPTO] Hidratar los datos en objetos del dominio (POO)
// Convertimos el arreglo plano del JSON en objetos Reserva con sus
// Horarios, para poder recorrerlos polimórficamente sin usar instanceof.
$totalReservas = 0;
$ingresoTotal = 0.0;
$totalPico = 0.0;
$totalEspacios = count($datos);

/**
 * Convierte el arreglo de un espacio en objetos Reserva.
 *
 * @param array<string, mixed> $espacio
 * @return Reserva[]
 */
function hidratarReservas(array $espacio): array
{
    $reservas = [];

    foreach ($espacio['reservas'] as $fila) {
        $horario = new Horario(
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $fila['fecha'] . ' ' . $fila['hora_inicio']
            ),
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $fila['fecha'] . ' ' . $fila['hora_fin']
            )
        );

        $reservas[] = new Reserva(
            $horario,
            (string) $fila['titular'],
            (float) $fila['costo'],
            (bool) $fila['es_pico']
        );
    }

    return $reservas;
}

// [CONCEPTO] Primera pasada: hidratar y acumular los totales del resumen
$espacios = [];

foreach ($datos as $infoEspacio) {
    $reservas = hidratarReservas($infoEspacio);
    $espacios[] = ['info' => $infoEspacio, 'reservas' => $reservas];

    foreach ($reservas as $reserva) {
        $totalReservas++;
        $ingresoTotal += $reserva->getCostoCalculado();

        if ($reserva->esPico()) {
            $totalPico += $reserva->getCostoCalculado();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reservas de Espacios</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .empty-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #ffc107;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .badge-pico {
            background-color: #fdecea;
            color: #c0392b;
        }

        .badge-ok {
            background-color: #eafaf1;
            color: #27ae60;
        }

        .resumen {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .card {
            flex: 1 1 180px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            border-radius: 4px;
            padding: 18px;
        }

        .card .valor {
            font-size: 1.8em;
            font-weight: 700;
            color: #2c3e50;
        }

        .card .etiqueta {
            color: #7f8c8d;
            font-size: 0.85em;
            margin-top: 4px;
        }

        .pie {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
            color: #95a5a6;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Sistema de Reservas de Espacios</h1>

        <?php if (!$hayArchivo): ?>
            <div class="empty-message">
                No se encontró el archivo <strong>data/reservas.json</strong>.
                Ejecutá primero <code>php main.php</code> para generar los datos.
            </div>
        <?php elseif (empty($datos)): ?>
            <div class="empty-message">No hay registros disponibles.</div>
        <?php else: ?>

            <h2>Resumen General</h2>
            <div class="resumen">
                <div class="card">
                    <div class="valor"><?= $totalEspacios ?></div>
                    <div class="etiqueta">Espacios registrados</div>
                </div>
                <div class="card">
                    <div class="valor"><?= $totalReservas ?></div>
                    <div class="etiqueta">Reservas totales</div>
                </div>
                <div class="card">
                    <div class="valor">$<?= number_format($ingresoTotal, 2, ',', '.') ?></div>
                    <div class="etiqueta">Ingresos acumulados</div>
                </div>
                <div class="card">
                    <div class="valor">$<?= number_format($totalPico, 2, ',', '.') ?></div>
                    <div class="etiqueta">Generado en horario pico</div>
                </div>
            </div>

            <h2>Listado de Reservas por Espacio</h2>

            <?php
                // [CONCEPTO] Polimorfismo: iterar los espacios sin instanceof
                foreach ($espacios as $espacio):
                    $infoEspacio = $espacio['info'];
                    $reservas = $espacio['reservas'];
                    $subtotal = 0.0;

                    foreach ($reservas as $reserva) {
                        $subtotal += $reserva->getCostoCalculado();
                    }
            ?>
                <h3 style="margin-top:22px;color:#2c3e50;">
                    <?= htmlspecialchars((string) $infoEspacio['espacio']) ?>
                    <span style="font-weight:400;color:#7f8c8d;font-size:0.8em;">
                        — <?= htmlspecialchars((string) $infoEspacio['tipo']) ?>
                        (capacidad: <?= (int) $infoEspacio['capacidad'] ?>)
                    </span>
                </h3>

                <?php if (empty($reservas)): ?>
                    <div class="empty-message" style="margin-top:10px;">
                        Este espacio no tiene reservas registradas.
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Titular</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Duración</th>
                                <th>Costo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><?= $reserva->getId() ?></td>
                                    <td><?= htmlspecialchars($reserva->getTitular()) ?></td>
                                    <td><?= htmlspecialchars($reserva->getHorario()->obtenerFecha()) ?></td>
                                    <td><?= htmlspecialchars((string) $reserva->getHorario()) ?></td>
                                    <td><?= $reserva->getHorario()->obtenerDuracionEnMinutos() ?> min</td>
                                    <td>$<?= number_format($reserva->getCostoCalculado(), 2, ',', '.') ?></td>
                                    <td>
                                        <span class="badge <?= $reserva->esPico() ? 'badge-pico' : 'badge-ok' ?>">
                                            <?= $reserva->esPico() ? 'PICO' : 'Normal' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="5" style="text-align:right;font-weight:600;">Subtotal</td>
                                <td colspan="2" style="font-weight:600;">
                                    $<?= number_format($subtotal, 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="pie">
                Datos leídos desde <code>data/reservas.json</code> ·
                <?= date('d/m/Y H:i') ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
