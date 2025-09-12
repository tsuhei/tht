<?php
// app/controllers/Admin/DashboardController.php

namespace App\Controllers\Admin;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Bloquear acceso si no hay sesión o no es admin
        if (
            empty($_SESSION['usuario']) ||
            ($_SESSION['usuario']['role'] ?? '') !== 'admin'
        ) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function index(): void
    {
        // Tomas siempre la clave correcta o un fallback
        $adminName = (string) (
            $_SESSION['usuario']['nom_usuario']
            ?? $_SESSION['usuario']['nom_usuario']
            ?? 'Invitado'
        );

        echo $this->view('admin/dashboard', [
            'adminName' => $adminName,
        ]);
    }
}
