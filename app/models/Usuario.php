<?php

namespace App\Models;

use App\Core\Database;

class Usuario
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Obtiene todos los usuarios con su rol.
     */
    public function all(): array
    {
        $this->db->query(
            "SELECT u.id, r.nom_rol, u.nom_usuario, u.correo
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id"
        );
        return $this->db->resultSet();
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $this->db->query(
            'SELECT *
        FROM usuarios
        WHERE correo = :correo
        LIMIT 1'
        );
        $this->db->bind(':correo', $correo);
        return $this->db->single() ?: null;
    }

    /**
     * Busca un usuario por su ID.
     */
    public function find(int $id): ?array
    {
        $this->db->query("SELECT * FROM usuarios WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single() ?: null;
    }

    /**
     * Verifica si ya existe un usuario con ese nombre.
     */
    public function usuarioExiste(string $nom_usuario): bool
    {
        $this->db->query("SELECT id FROM usuarios WHERE nom_usuario = :nom_usuario");
        $this->db->bind(':nom_usuario', $nom_usuario);
        return (bool)$this->db->single();
    }

    /**
     * Verifica si ya existe un usuario con ese correo.
     */
    public function correoExiste(string $correo): bool
    {
        $this->db->query("SELECT id FROM usuarios WHERE correo = :correo");
        $this->db->bind(':correo', $correo);
        return (bool)$this->db->single();
    }

    /**
     * Crea un nuevo usuario.
     * Espera un array con id_rol, nom_usuario, correo y contrasena.
     */
    public function create(array $data): bool
    {
        $this->db->query(
            "INSERT INTO usuarios
            (id_rol, nom_usuario, correo, contrasena, created_at)
            VALUES
            (:id_rol, :nom_usuario, :correo, :contrasena, NOW())"
        );

        $this->db->bind(':id_rol', $data['id_rol']);
        $this->db->bind(':nom_usuario', $data['nom_usuario']);
        $this->db->bind(':correo', $data['correo']);
        $this->db->bind(
            ':contrasena',
            password_hash($data['contrasena'], PASSWORD_BCRYPT)
        );

        return $this->db->execute();
    }

    /**
     * Actualiza un usuario existente.
     * Si no viene 'contrasena' o está vacío, no la modifica.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE usuarios
                SET id_rol      = :id_rol,
                    nom_usuario = :nom_usuario,
                    correo      = :correo";

        if (!empty($data['contrasena'])) {
            $sql .= ", contrasena = :contrasena";
            $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";

        $this->db->query($sql);
        $this->db->bind(':id_rol',      $data['id_rol']);
        $this->db->bind(':nom_usuario', $data['nom_usuario']);
        $this->db->bind(':correo',      $data['correo']);

        if (isset($data['contrasena'])) {
            $this->db->bind(':contrasena', $data['contrasena']);
        }

        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    /**
     * Elimina un usuario por su ID.
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM usuarios WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Búsqueda por ID, nombre de usuario o rol.
     */
    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $this->db->query(
            "SELECT u.id, r.nom_rol, u.nom_usuario, u.correo
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id
            WHERE u.id LIKE :like
            OR u.nom_usuario LIKE :like
            OR r.nom_rol LIKE :like"
        );
        $this->db->bind(':like', $like);
        return $this->db->resultSet();
    }

    /**
     * Verifica si un valor existe en un campo, excluyendo un ID específico.
     */
    public function exists(string $field, string $value, ?int $excludeId = null): bool
    {
        $sql  = "SELECT COUNT(*) FROM usuarios WHERE {$field} = :value";
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }
        $this->db->query($sql);
        $this->db->bind(':value', $value);
        if ($excludeId !== null) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        return (bool) $this->db->single()['COUNT(*)'];
    }

    public function updateNombre(int $id, string $nuevoNombre): bool
    {
        $this->db->query("UPDATE usuarios SET nom_usuario = :nom_usuario, updated_at = NOW() WHERE id = :id");
        $this->db->bind(':nom_usuario', $nuevoNombre);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $this->db->query("UPDATE usuarios SET contrasena = :hash, updated_at = NOW() WHERE id = :id");
        $this->db->bind(':hash', $hash);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getSenasVistasPorUsuarioYCategoria(int $userId, int $categoriaId): array
{
    $db = new Database();
    $db->query("SELECT senas_vistas FROM progresos 
               WHERE id_usuario = :user_id AND id_categoria = :categoria_id");
    $db->bind(':user_id', $userId);
    $db->bind(':categoria_id', $categoriaId);
    $result = $db->single();
    
    if ($result && !empty($result['senas_vistas'])) {
        return json_decode($result['senas_vistas'], true) ?? [];
    }
    
    return [];
}
}
