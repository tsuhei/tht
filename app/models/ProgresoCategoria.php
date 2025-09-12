<?php

namespace App\Models;

use App\Core\Database;

class ProgresoCategoria
{
    public static function exists(int $userId, int $catId): bool
    {
        $db = new Database();
        $db->query("SELECT COUNT(*) AS total
    FROM progresos
    WHERE id_usuario = :u
    AND id_categoria = :c");
        $db->bind(':u', $userId);
        $db->bind(':c', $catId);
        $row = $db->single();
        return !empty($row['total']);
    }

    public static function add(int $userId, int $catId): void
    {
        $db = new Database();
        $db->query("INSERT INTO progresos (id_usuario, id_categoria, senas_vistas)
    VALUES (:u, :c, '[]')");
        $db->bind(':u', $userId);
        $db->bind(':c', $catId);
        $db->execute();
    }

    public static function getByUser(int $userId): array
    {
        $db = new Database();
        $db->query("SELECT 
        pc.created_at, 
        c.nom_categoria,
        pc.senas_vistas
    FROM progresos pc
    JOIN categorias c ON pc.id_categoria = c.id
    WHERE pc.id_usuario = :u
    ORDER BY pc.created_at ASC");
        $db->bind(':u', $userId);
        return $db->resultSet();
    }

    public static function getProgresoPorCategoria(int $userId): array
    {
        $db = new Database();

        $db->query("SELECT
        c.id,
        c.nom_categoria,
        c.icono,
        (SELECT COUNT(*) FROM senas s WHERE s.id_categoria = c.id) AS total_senas,
        (SELECT COUNT(*) FROM JSON_TABLE(pc.senas_vistas, '$[*]' COLUMNS(sena_id INT PATH '$')) AS senas 
         FROM progresos pc 
         WHERE pc.id_usuario = :userId AND pc.id_categoria = c.id) AS senas_completadas
    FROM categorias c");
        $db->bind(':userId', $userId);

        $results = $db->resultSet();

        foreach ($results as &$row) {
            if ($row['total_senas'] > 0) {
                $row['progreso'] = ($row['senas_completadas'] / $row['total_senas']) * 100;
            } else {
                $row['progreso'] = 0;
            }
        }

        return $results;
    }

    public static function getAll(): array
    {
        $db = new Database();
        $db->query("SELECT 
        CONCAT(pc.id_usuario, '-', pc.id_categoria) as id,
        u.nom_usuario AS usuario, 
        c.nom_categoria AS categoria, 
        pc.senas_vistas,
        pc.created_at,
        pc.updated_at
    FROM progresos pc
    JOIN usuarios u ON pc.id_usuario = u.id
    JOIN categorias c ON pc.id_categoria = c.id
    ORDER BY pc.created_at ASC");
        return $db->resultSet();
    }

    
}
