<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Categoria
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Obtiene todas las categorías.
     * Si se pasa $term, filtra por ID o nombre.
     */
    public function all(string $term = ''): array
    {
        if (empty($term)) {
            $this->db->query(
                "SELECT * FROM categorias ORDER BY id ASC"
            );
        } else {
            $this->db->query(
                "SELECT * FROM categorias
                 WHERE CAST(id AS CHAR) LIKE :like
                    OR nom_categoria LIKE :like
                 ORDER BY id ASC"
            );
            $this->db->bind(':like', "%{$term}%");
        }

        return $this->db->resultSet();
    }

    /**
     * Busca una categoría por su ID.
     */
    public function find(int $id): ?array
    {
        $this->db->query("SELECT * FROM categorias WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single() ?: null;
    }

    /**
     * Inserta una nueva categoría.
     * Espera ['nom_categoria'=>string, 'video_url'=>string, 'icono'=>string].
     */
    public function create(array $data): bool
    {
        $this->db->query(
            "INSERT INTO categorias (nom_categoria, video_url, icono, created_at)
             VALUES (:nom_categoria, :video_url, :icono, NOW())"
        );
        $this->db->bind(':nom_categoria', $data['nom_categoria']);
        $this->db->bind(':video_url',     $data['video_url'] ?? '');
        $this->db->bind(':icono',         $data['icono'] ?? '');

        return $this->db->execute();
    }

    /**
     * Actualiza una categoría existente.
     */
    public function update(int $id, array $data): bool
    {
        $this->db->query(
            "UPDATE categorias SET
                nom_categoria = :nom_categoria,
                video_url     = :video_url,
                icono         = :icono,
                updated_at    = NOW()
             WHERE id = :id"
        );
        $this->db->bind(':nom_categoria', $data['nom_categoria']);
        $this->db->bind(':video_url',     $data['video_url'] ?? '');
        $this->db->bind(':icono',         $data['icono'] ?? '');
        $this->db->bind(':id',            $id);

        return $this->db->execute();
    }

    /**
     * Elimina una categoría por su ID.
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM categorias WHERE id = :id");
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    /**
     * Busca categorías por término en ID o nombre.
     */
    public function search(string $term): array
    {
        $this->db->query(
            "SELECT * FROM categorias
             WHERE CAST(id AS CHAR) LIKE :like
                OR nom_categoria LIKE :like
             ORDER BY id DESC"
        );
        $this->db->bind(':like', "%{$term}%");

        return $this->db->resultSet();
    }

    /**
     * Recupera varias categorías a partir de un array de IDs.
     */
    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = [];
        foreach (array_keys($ids) as $i) {
            $placeholders[] = ":id{$i}";
        }
        $in = implode(',', $placeholders);

        $this->db->query(
            "SELECT id, nom_categoria FROM categorias WHERE id IN ({$in}) ORDER BY id ASC"
        );

        foreach ($ids as $i => $catId) {
            $this->db->bind(":id{$i}", $catId, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }

    /**
     * Obtiene todas las categorías ordenadas por nombre.
     * Método estático para uso general.
     */
    public static function getAll(): array
    {
        $db = new Database();
        $db->query("SELECT * FROM categorias ORDER BY nom_categoria");
        return $db->resultSet();
    }

    /**
     * Obtiene preguntas asociadas a una categoría.
     */
    public function getPreguntasPorCategoria(int $categoriaId): array
    {
        $this->db->query("SELECT * FROM preguntas WHERE categoria_id = :catId");
        $this->db->bind(':catId', $categoriaId);
        return $this->db->resultSet();
    }

    
}
