<?php
// core/Controller.php
namespace App\Core;

use Exception;

class Controller
{

        public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    // Carga un modelo dinámicamente
    public function loadModel(string $model)
    {
        $fqcn = "App\\Models\\{$model}";
        $file = __DIR__ . "/../app/models/{$model}.php";

        if (! class_exists($fqcn) && file_exists($file)) {
            require_once $file;
        }

        if (! class_exists($fqcn)) {
            throw new Exception("Model {$fqcn} no encontrado");
        }

        return new $fqcn();
    }

    public function view(string $view, array $data = []): string
    {
        $viewFile = __DIR__ . "/../app/views/{$view}.php";

        if (! file_exists($viewFile)) {
            throw new Exception("View {$view} no encontrada");
        }

        extract($data);
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    public function ensureAdmin(): void
{
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}

    
}
