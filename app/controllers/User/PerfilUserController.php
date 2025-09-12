<?php
namespace App\Controllers\User;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Categoria; // Necesario para el progreso de categorías
use App\Models\Sena;      // Necesario para el progreso de categorías

class PerfilUserController extends Controller
{
    private Usuario $usuarioModel;
    private Categoria $categoriaModel;
    private Sena $senaModel;

    public function __construct()
    {
        parent::__construct();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

                if (empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
}


        $this->usuarioModel = new Usuario();
        $this->categoriaModel = new Categoria();
        $this->senaModel = new Sena();
    }

    public function index(): void
{
    $userId = $_SESSION['usuario']['id'] ?? null;
    if (!$userId) {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    $user = $this->usuarioModel->find((int)$userId);
    $userName = $user['nom_usuario'] ?? 'Usuario';
    $active = 'perfil';

    $categoriasProgreso = [];
    $allCategories = $this->categoriaModel->all();

    foreach ($allCategories as $categoria) {
        $totalSenasCategoria = count($this->senaModel->getByCategoriaId((int)$categoria['id']));
        $progresoModel = new \App\Models\ProgresoSena();
$senasVistasUsuario = $progresoModel->getSenasVistas((int)$userId, (int)$categoria['id']);
$countSenasVistas = count($senasVistasUsuario);

        $progreso = 0;
        if ($totalSenasCategoria > 0) {
            $progreso = ($countSenasVistas / $totalSenasCategoria) * 100;
        }

        $categoriasProgreso[] = [
            'id' => $categoria['id'],
            'nom_categoria' => $categoria['nom_categoria'],
            'icono' => $categoria['icono'],
            'progreso' => round($progreso),
        ];
    }

    echo $this->view('usuarios/perfil_user', [
        'user' => $user,
        'userName' => $userName,
        'active' => $active,
        'categoriasProgreso' => $categoriasProgreso,
    ]);
}


    public function editarNombre(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $nuevoNombre = trim($_POST['nombre_usuario'] ?? '');

        if ($nuevoNombre === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'El nombre no puede estar vacío.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        $userId = $_SESSION['usuario']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Sesión inválida.'];
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Verificar si el nuevo nombre ya existe para otro usuario
        if ($this->usuarioModel->exists('nom_usuario', $nuevoNombre, (int)$userId)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'El nombre de usuario ya está en uso.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        $actualizado = $this->usuarioModel->updateNombre((int)$userId, $nuevoNombre);

        if ($actualizado) {
            $_SESSION['usuario']['nom_usuario'] = $nuevoNombre; // actualizar sesión
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nombre actualizado correctamente.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error al actualizar el nombre.'];
        }

        header('Location: ' . BASE_URL . 'usuarios/perfil');
        exit;
    }

    public function cambiarPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $passwordActual = trim($_POST['password_actual'] ?? '');
        $passwordNueva  = trim($_POST['password_nueva'] ?? '');
        $passwordConf    = trim($_POST['password_confirmar'] ?? '');

        if ($passwordActual === '' || $passwordNueva === '' || $passwordConf === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Todos los campos son obligatorios.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        if ($passwordNueva !== $passwordConf) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La nueva contraseña y la confirmación no coinciden.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        if (strlen($passwordNueva) < 6) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La contraseña debe tener al menos 6 caracteres.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }
        // Opcional: Añadir validación de complejidad (mayúsculas, minúsculas, números, símbolos)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $passwordNueva)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La nueva contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula, un número y un símbolo.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }


        $userId = $_SESSION['usuario']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Sesión inválida.'];
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $user = $this->usuarioModel->find((int)$userId);
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Usuario no encontrado.'];
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Verificar contraseña actual
        if (!password_verify($passwordActual, $user['contrasena'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'La contraseña actual no es correcta.'];
            header('Location: ' . BASE_URL . 'usuarios/perfil');
            exit;
        }

        $hash = password_hash($passwordNueva, PASSWORD_DEFAULT);

        $updated = $this->usuarioModel->updatePassword((int)$userId, $hash);

        if ($updated) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Contraseña actualizada correctamente.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error al cambiar la contraseña.'];
        }

        header('Location: ' . BASE_URL . 'usuarios/perfil');
        exit;
    }
}
