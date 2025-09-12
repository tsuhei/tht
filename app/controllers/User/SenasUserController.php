<?php

namespace App\Controllers\User;

use App\Core\Controller;
use App\Models\Sena;
use App\Models\ProgresoSena;

class SenasUserController extends Controller
{
    public function index(int $id_categoria): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $categoriaModel = new \App\Models\Categoria();
        $senaModel = new Sena();

        $categoria = $categoriaModel->find($id_categoria);
        if (!$categoria) {
            header('Location: ' . BASE_URL . 'usuarios/categorias');
            exit;
        }

        $senas = $senaModel->getByCategoriaId($id_categoria);

        $userName = $_SESSION['usuario']['nom_usuario'] ?? 'Usuario';
        $active = 'cat';

        echo $this->view('usuarios/senas_user', [
            'userName' => $userName,
            'active' => $active,
            'categoria' => $categoria,
            'senas' => $senas,
        ]);
    }

    // SenasUserController.php

public function registrarProgreso(): void
{
    // Las validaciones iniciales permanecen igual
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['usuario'])) {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
        exit;
    }

    $userId = $_SESSION['usuario']['id'];
    $idSena = (int)($_POST['id_sena'] ?? 0);
    $categoriaId = (int)($_POST['id_categoria'] ?? 0);

    if ($idSena <= 0 || $categoriaId <= 0) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
        exit;
    }

    // El registro del progreso no cambia
    $progresoModel = new \App\Models\ProgresoSena();
    $progresoModel->record($userId, $idSena, $categoriaId);

    // ---- CAMBIO IMPORTANTE ----
    // Eliminamos la redirección y en su lugar enviamos una respuesta JSON.
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Progreso registrado.']);
    exit;
}

}
