<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Validators\AuthValidator;

class AuthController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->loadModel('Usuario');
    }

    public function index(): void
    {
        // Mostrar formulario de login por defecto
        $this->showLoginForm();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Recolectar datos
        $data = [
            'correo'     => trim($_POST['correo'] ?? ''),
            'contrasena' => trim($_POST['contrasena'] ?? ''),
        ];

        // 2. Validar campos vacíos
        $errors = AuthValidator::validateLogin($data);
        if (!empty($errors)) {
            echo $this->view('auth', [
                'mode'   => 'login',
                'errors' => $errors,
                'old'    => $data,
            ]);
            return;
        }

        // 3. Buscar usuario por correo
        $usuario = $this->model->buscarPorCorreo($data['correo']);

        if (!$usuario) {
            $errors['general'] = 'El usuario no existe';
            echo $this->view('auth', [
                'mode'   => 'login',
                'errors' => $errors,
                'old'    => $data,
            ]);
            return;
        }

        // 4. Verificar contraseña (importante: usar password_verify)
        if (!password_verify($data['contrasena'], $usuario['contrasena'])) {
            $errors['general'] = 'Contraseña incorrecta';
            echo $this->view('auth', [
                'mode'   => 'login',
                'errors' => $errors,
                'old'    => $data,
            ]);
            return;
        }

        // 5. Guardar sesión (mejor tomar rol desde la BD)
        $_SESSION['usuario'] = [
            'id'          => $usuario['id'],
            'nom_usuario' => $usuario['nom_usuario'],
            'correo'      => $usuario['correo'],
            'role'        => ($usuario['id_rol'] == 1 ? 'admin' : 'user'),
        ];

        // 6. Redirigir según rol
        $redirect = ($_SESSION['usuario']['role'] === 'admin')
            ? BASE_URL . 'admin/dashboard'
            : BASE_URL . 'usuarios/main';

        header('Location: ' . $redirect);
        exit;
    }

    public function showLoginForm(): void
    {
        echo $this->view('auth', ['mode' => 'login']);
    }


    public function showRegisterForm(): void
    {
        echo $this->view('auth', ['mode' => 'register']);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }

        $data = [
            'nom_usuario'    => trim($_POST['nom_usuario'] ?? ''),
            'correo'         => trim($_POST['correo'] ?? ''),
            'contrasena'     => trim($_POST['contrasena'] ?? ''),
            'confirm_contra' => trim($_POST['confirm_contra'] ?? '')
        ];

        $errors = AuthValidator::validateRegister($data);

        // Confirmación de contraseña (en caso de que el validador no lo maneje)
        if ($data['contrasena'] !== $data['confirm_contra']) {
            $errors['confirm_contra'] = 'Las contraseñas no coinciden';
        }

        if (empty($errors) && $this->model->usuarioExiste($data['nom_usuario'])) {
            $errors['nom_usuario'] = 'El usuario ya está registrado';
        }
        if (empty($errors) && $this->model->correoExiste($data['correo'])) {
            $errors['correo'] = 'El correo ya está registrado';
        }

        if (!empty($errors)) {
            echo $this->view('auth', [
                'mode'   => 'register',
                'errors' => $errors,
                'old'    => $data
            ]);
            return;
        }

        // Rol: admin=1, user=2
        $rol  = (strpos($data['correo'], 'tht') !== false) ? 1 : 2;
        $userData = [
            'id_rol'      => $rol,
            'nom_usuario' => $data['nom_usuario'],
            'correo'      => $data['correo'],
            'contrasena'  => $data['contrasena'], // cruda, el modelo la hashea
        ];

        $success = $this->model->create($userData);

        if ($success) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        } else {
            $errors['general'] = 'Error al registrar usuario';
            echo $this->view('auth', [
                'mode'   => 'register',
                'errors' => $errors,
                'old'    => $data
            ]);
        }
    }


    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}
