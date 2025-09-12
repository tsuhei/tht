<?php
namespace App\Controllers\Admin;

use App\Models\Desbloqueo;

class DesbloqueosController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $adminName   = $_SESSION['usuario']['nom_usuario'] ?? 'Invitado';
        $desbloqueos = Desbloqueo::getAll();

        require_once __DIR__ . '/../../views/admin/desbloqueos.php';
    }
}
