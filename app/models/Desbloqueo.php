<?php
namespace App\Models;

use App\Core\Database;

class Desbloqueo
{
    public static function getAll(): array
    {
        $db = new Database();

        // IDs por defecto (Abecedario y Números)
        $defaultCatIds  = [1, 2];
        $defaultTestIds = [1, 2];

        $sql = "
            SELECT
                u.nom_usuario                    AS usuario,
                t1.nombre_test                   AS test_aprobado,
                dt.created_at                    AS fecha_aprobado,
                c.nom_categoria                  AS categoria_desbloqueada,
                t2.nombre_test                   AS test_desbloqueado,
                dc.created_at                    AS fecha_desbloqueo
            FROM desbloquear_tests dt
            INNER JOIN usuarios u 
                ON dt.id_usuario = u.id
            INNER JOIN tests t1 
                ON dt.id_test = t1.id
            INNER JOIN desbloquear_categorias dc
                ON dc.id_usuario        = u.id
               AND DATE(dc.created_at) = DATE(dt.created_at)
            INNER JOIN categorias c 
                ON dc.id_categoria = c.id
            INNER JOIN tests t2 
                ON t2.id_categoria = c.id
               AND t2.id != t1.id
            WHERE u.id_rol = 2                                     -- <--- filtrar por id_rol
              AND c.id  NOT IN(" . implode(',', $defaultCatIds)  . ")
              AND t2.id NOT IN(" . implode(',', $defaultTestIds) . ")
            ORDER BY dt.created_at ASC
        ";

        $db->query($sql);
        return $db->resultSet();
    }
}
