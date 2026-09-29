<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexión a la base de datos MySQL usando PDO.
 * Todas las consultas de la aplicación se apoyan en esta clase.
 */
final class Database
{
    private static ?PDO $conexion = null;

    /**
     * Configuración leída de variables de entorno con valores por defecto,
     * para que el mismo código funcione en local y en Docker.
     */
    public static function obtenerConexion(): PDO
    {
        if (self::$conexion instanceof PDO) {
            return self::$conexion;
        }

        $host = self::variable('DB_HOST', '127.0.0.1');
        $puerto = self::variable('DB_PORT', '3306');
        $nombre = self::variable('DB_NAME', 'gestor_contactos');
        $usuario = self::variable('DB_USER', 'root');
        $clave = self::variable('DB_PASS', '');

        $dsn = "mysql:host={$host};port={$puerto};dbname={$nombre};charset=utf8mb4";

        try {
            self::$conexion = new PDO($dsn, $usuario, $clave, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No se pudo conectar con la base de datos: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        return self::$conexion;
    }

    private static function variable(string $nombre, string $porDefecto): string
    {
        $valor = getenv($nombre);

        return ($valor === false || $valor === '') ? $porDefecto : $valor;
    }
}
