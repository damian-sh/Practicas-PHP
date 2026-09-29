<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Repositorio de contactos.
 * Todas las consultas usan consultas preparadas (PDO), nunca concatenación
 * de cadenas, para evitar inyección SQL.
 */
final class ContactoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Listado de contactos con el nombre de su tipo.
     * Si se pasa $tipoId, filtra por ese tipo de contacto.
     *
     * @return array<int, array<string, mixed>>
     */
    public function obtenerTodos(?int $tipoId = null): array
    {
        $sql = 'SELECT c.id, c.nombre, c.email, c.telefono, c.fecha_creacion,
                       t.id AS tipo_id, t.nombre AS tipo_nombre
                FROM contactos c
                INNER JOIN tipos_contacto t ON c.tipo_contacto_id = t.id';

        $parametros = [];

        if ($tipoId !== null) {
            $sql .= ' WHERE c.tipo_contacto_id = :tipo_id';
            $parametros[':tipo_id'] = $tipoId;
        }

        $sql .= ' ORDER BY c.nombre';

        $statement = $this->pdo->prepare($sql);

        foreach ($parametros as $clave => $valor) {
            $statement->bindValue($clave, $valor, PDO::PARAM_INT);
        }

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = 'SELECT c.id, c.nombre, c.email, c.telefono, c.tipo_contacto_id,
                       c.fecha_creacion, t.nombre AS tipo_nombre
                FROM contactos c
                INNER JOIN tipos_contacto t ON c.tipo_contacto_id = t.id
                WHERE c.id = :id';

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $fila = $statement->fetch();

        return $fila === false ? null : $fila;
    }

    public function existe(int $id): bool
    {
        $sql = 'SELECT COUNT(*) FROM contactos WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Verifica si el email ya está registrado.
     * $ignorarId permite excluir al propio contacto al validar una edición.
     */
    public function emailExiste(string $email, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM contactos WHERE email = :email';
        $parametros = [':email' => $email];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> :id';
            $parametros[':id'] = $ignorarId;
        }

        $statement = $this->pdo->prepare($sql);

        foreach ($parametros as $clave => $valor) {
            $statement->bindValue($clave, $valor);
        }

        $statement->execute();

        return (int) $statement->fetchColumn() > 0;
    }

    public function crear(string $nombre, string $email, string $telefono, int $tipoId): int
    {
        $sql = 'INSERT INTO contactos (nombre, email, telefono, tipo_contacto_id)
                VALUES (:nombre, :email, :telefono, :tipo_id)';

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':nombre', $nombre);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':telefono', $telefono);
        $statement->bindValue(':tipo_id', $tipoId, PDO::PARAM_INT);
        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(
        int $id,
        string $nombre,
        string $email,
        string $telefono,
        int $tipoId
    ): bool {
        $sql = 'UPDATE contactos
                SET nombre = :nombre,
                    email = :email,
                    telefono = :telefono,
                    tipo_contacto_id = :tipo_id
                WHERE id = :id';

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':nombre', $nombre);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':telefono', $telefono);
        $statement->bindValue(':tipo_id', $tipoId, PDO::PARAM_INT);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function eliminar(int $id): bool
    {
        $sql = 'DELETE FROM contactos WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function contarTodos(): int
    {
        $statement = $this->pdo->query('SELECT COUNT(*) FROM contactos');

        return (int) $statement->fetchColumn();
    }
}
