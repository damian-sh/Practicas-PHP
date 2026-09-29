<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Repositorio del catálogo de tipos de contacto.
 */
final class TipoContactoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array<int, array{id: int, nombre: string}>
     */
    public function obtenerTodos(): array
    {
        $sql = 'SELECT id, nombre FROM tipos_contacto ORDER BY nombre';
        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function existe(int $id): bool
    {
        $sql = 'SELECT COUNT(*) FROM tipos_contacto WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return (int) $statement->fetchColumn() > 0;
    }
}
