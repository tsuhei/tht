<?php
namespace App\Controllers\User;

use App\Core\Controller;

class MainController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // No es necesario llamar session_start() aquí porque el padre ya lo hace

        if (empty($_SESSION['usuario']) || ($_SESSION['usuario']['role'] ?? '') === 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    /**
     * Muestra la pantalla principal tras login
     */
    public function index(): void
    {
        // Usar la clave consistente para nombre de usuario
        $userName = $_SESSION['usuario']['nom_usuario'] ?? 'Usuario';
        $active   = 'main'; // Marca el enlace Información como activo

        require __DIR__ . '/../../views/usuarios/main.php';
    }
}
