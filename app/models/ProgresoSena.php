<?php

namespace App\Models;

use App\Core\Database;

class ProgresoSena
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function record(int $userId, int $senaId, int $categoriaId): void
    {
        $this->db->query("SELECT senas_vistas FROM progresos 
                     WHERE id_usuario = :user_id AND id_categoria = :categoria_id");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':categoria_id', $categoriaId);
        $existing = $this->db->single();

        if ($existing) {
            // Actualizar registro existente
            $senasVistas = json_decode($existing['senas_vistas'], true) ?? [];

            if (in_array($senaId, $senasVistas)) {
                return; // Ya está registrada
            }

            $senasVistas[] = $senaId;
            $senasVistasJson = json_encode($senasVistas);

            $this->db->query("UPDATE progresos 
                         SET senas_vistas = :senas_vistas, updated_at = NOW()
                         WHERE id_usuario = :user_id AND id_categoria = :categoria_id");
            $this->db->bind(':senas_vistas', $senasVistasJson);
        } else {
            // Crear nuevo registro
            $senasVistas = [$senaId];
            $senasVistasJson = json_encode($senasVistas);

            $this->db->query("INSERT INTO progresos 
                         (id_usuario, id_categoria, senas_vistas, created_at, updated_at)
                         VALUES (:user_id, :categoria_id, :senas_vistas, NOW(), NOW())");
            $this->db->bind(':senas_vistas', $senasVistasJson);
        }

        $this->db->bind(':user_id', $userId);
        $this->db->bind(':categoria_id', $categoriaId);
        $this->db->execute();
    }

    public function getSenasVistas(int $userId, int $categoriaId): array
{
    $this->db->query("SELECT senas_vistas FROM progresos 
                     WHERE id_usuario = :user_id AND id_categoria = :categoria_id");
    $this->db->bind(':user_id', $userId);
    $this->db->bind(':categoria_id', $categoriaId);
    $result = $this->db->single();
    
    if ($result && !empty($result['senas_vistas'])) {
        return json_decode($result['senas_vistas'], true) ?? [];
    }
    
    return [];
}
}
