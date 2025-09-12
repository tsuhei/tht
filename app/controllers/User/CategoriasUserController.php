<?php
namespace App\Controllers\User;

use App\Core\Controller;
use App\Models\Categoria;

class CategoriasUserController extends Controller
{
    /** @var Categoria */
    private $model;

    public function __construct()
    {
        parent::__construct();

        // Iniciar sesión si no está activa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Validar que el usuario esté logueado
        if (empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Instanciar el modelo de Categoría
        $this->model = new Categoria();
    }

    public function index(): void
    {
        // Datos para el layout
        $userName = $_SESSION['usuario']['nombre'] ?? 'Usuario';
        $active   = 'cat';

        // (Opcional) término de búsqueda
        $term = trim($_GET['q'] ?? '');

        // Traer siempre un array (aunque esté vacío)
        $categories = $this->model->all($term);

        // Cargar la vista con $userName, $active y $categories disponibles
        require __DIR__ . '/../../views/usuarios/categorias_user.php';
    }
}
