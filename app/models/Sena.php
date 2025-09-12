<?php

namespace App\Models;

use App\Core\Database;

class Sena
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Obtiene todas las señas con el nombre de su categoría.
     * Si se pasa $term, filtra por palabra o categoría.
     */
    public function all(string $term = ''): array
    {
        if (empty($term)) {
            $this->db->query(
                "SELECT s.id, c.nom_categoria, s.palabra, s.descripcion, s.media_url
                   FROM senas s
                   JOIN categorias c ON s.id_categoria = c.id
                  ORDER BY s.id ASC"
            );
        } else {
            $this->db->query(
                "SELECT s.id, c.nom_categoria, s.palabra, s.descripcion, s.media_url
                   FROM senas s
                   JOIN categorias c ON s.id_categoria = c.id
                  WHERE s.palabra LIKE :like
                     OR c.nom_categoria LIKE :like
                  ORDER BY s.id ASC"
            );
            $like = '%' . $term . '%';
            $this->db->bind(':like', $like);
        }

        return $this->db->resultSet();
    }

    /**
     * Busca una seña por su ID.
     */
    public function find(int $id): ?array
    {
        $this->db->query("SELECT * FROM senas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single() ?: null;
    }

    /**
     * Inserta una nueva seña.
     */
    public function create(array $data): bool
    {
        $this->db->query(
            "INSERT INTO senas
                (id_categoria, palabra, descripcion, media_url, created_at)
             VALUES
                (:id_categoria, :palabra, :descripcion, :media_url, NOW())"
        );
        $this->db->bind(':id_categoria', $data['id_categoria']);
        $this->db->bind(':palabra',      $data['palabra']);
        $this->db->bind(':descripcion',  $data['descripcion']);
        $this->db->bind(':media_url',    $data['media_url'] ?? '');

        return $this->db->execute();
    }

    /**
     * Actualiza una seña existente.
     */
    public function update(int $id, array $data): bool
    {
        $this->db->query(
            "UPDATE senas
                SET id_categoria = :id_categoria,
                    palabra      = :palabra,
                    descripcion  = :descripcion,
                    media_url    = :media_url,
                    updated_at   = NOW()
              WHERE id = :id"
        );
        $this->db->bind(':id_categoria', $data['id_categoria']);
        $this->db->bind(':palabra',      $data['palabra']);
        $this->db->bind(':descripcion',  $data['descripcion']);
        $this->db->bind(':media_url',    $data['media_url'] ?? '');
        $this->db->bind(':id',            $id);

        return $this->db->execute();
    }

    /**
     * Elimina una seña por su ID.
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM senas WHERE id = :id");
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    /**
     * Obtiene todas las señas de una categoría específica.
     */
    public function getByCategoriaId(int $id_categoria): array
    {
        $this->db->query(
            "SELECT *
               FROM senas
              WHERE id_categoria = :id_categoria
           ORDER BY id ASC"
        );
        $this->db->bind(':id_categoria', $id_categoria);
        return $this->db->resultSet();
    }

    /**
     * Obtiene todas las señas con su categoría (para listados completos).
     */
    public function allWithCategory(): array
    {
        $this->db->query("
            SELECT s.*, c.nom_categoria AS categoria
            FROM senas s
            JOIN categorias c ON s.id_categoria = c.id
            ORDER BY s.id ASC
        ");
        return $this->db->resultSet();
    }
}
