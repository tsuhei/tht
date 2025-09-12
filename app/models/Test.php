<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Test
{
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Devuelve todos los tests junto al nombre de su categoría
     */
    public function all(): array
    {
        $this->db->query("
            SELECT
                t.id,
                t.nombre_test,
                t.id_categoria,
                c.nom_categoria
            FROM tests AS t
            JOIN categorias AS c
              ON t.id_categoria = c.id
            ORDER BY t.id ASC
        ");

        return $this->db->resultSet();
    }

    /**
     * Busca un test por su ID
     */
    public function find(int $id): ?array
    {
        $this->db->query("SELECT * FROM tests WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single() ?: null;
    }

    /**
     * Inserta un nuevo test
     */
    public function create(array $data): bool
    {
        $this->db->query("
            INSERT INTO tests
                (nombre_test, id_categoria, created_at)
            VALUES
                (:nombre_test, :id_categoria, NOW())
        ");
        $this->db->bind(':nombre_test', $data['nombre_test']);
        $this->db->bind(':id_categoria', $data['id_categoria'], PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Actualiza un test existente
     */
    public function update(int $id, array $data): bool
    {
        $this->db->query("
            UPDATE tests
               SET nombre_test  = :nombre_test,
                   id_categoria = :id_categoria,
                   updated_at   = NOW()
             WHERE id = :id
        ");
        $this->db->bind(':nombre_test', $data['nombre_test']);
        $this->db->bind(':id_categoria', $data['id_categoria'], PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Elimina un test
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM tests WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Obtiene todos los tests asociados a una categoría específica.
     * @param int $catId El ID de la categoría.
     * @return array Un array de tests.
     */
    public function getByCategory(int $catId): array
    {
        $this->db->query("
        SELECT id AS id_test, nombre_test
        FROM tests
        WHERE id_categoria = :cid
        ORDER BY id ASC
    ");
        $this->db->bind(':cid', $catId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }


    public static function getAll(): array
    {
        $db = new Database();
        $db->query("SELECT * FROM tests ORDER BY id ASC");
        return $db->resultSet();
    }
}
