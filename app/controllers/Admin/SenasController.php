<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Sena;
use App\Models\Categoria; // Importar el modelo Categoria

class SenasController extends Controller
{
    protected Sena $model;

    protected string $uploadDir;
    protected const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    protected array $allowedMimes = [
        'video/mp4',
        'video/webm',
        'video/ogg'
    ];

    public function __construct()
    {
        parent::__construct();

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__, 3)); // Ajusta según estructura de carpetas
        }

        $this->uploadDir = ROOT_PATH . '/public/videos/';
        $this->ensureAdmin();
        $this->model = new Sena();
    }

    public function ensureAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $usr = $_SESSION['usuario'] ?? [];
        if (empty($usr) || ($usr['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    private function flash(string $key, string $msg): void
    {
        $_SESSION['flash'][$key] = $msg;
    }

    protected function getFlash(string $key): ?string
    {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }

    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $adminName = $_SESSION['usuario']['nom_usuario']
            ?? $_SESSION['usuario']['nombre']
            ?? 'Invitado';

        $term  = trim($_GET['q'] ?? '');
        $senas = $this->model->all($term);

        $showCreate = isset($_GET['create']); // Variable para controlar la visibilidad del modal de creación
        $showEdit   = isset($_GET['edit']);   // Variable para controlar la visibilidad del modal de edición
        $showModal  = $showCreate || $showEdit; // Variable para controlar la visibilidad general del modal

        $sena = null; // Inicializar $sena a null por defecto
        if ($showEdit) {
            $sena = $this->model->find((int) $_GET['edit']);
        }

        // Obtener categorías para el select en el formulario
        $categoriaModel = new Categoria();
        $cats = $categoriaModel->all();

        // Mensajes flash (error/success)
        $error   = $this->getFlash('error');
        $success = $this->getFlash('success');

        // Pasar todas las variables necesarias a la vista
        echo $this->view('admin/senas', [
            'adminName'  => $adminName,
            'term'       => $term,
            'senas'      => $senas,
            'showCreate' => $showCreate,
            'showEdit'   => $showEdit,
            'showModal'  => $showModal,
            'sena'       => $sena,
            'cats'       => $cats, // Pasar las categorías
            'error'      => $error,
            'success'    => $success
        ]);
    }

    public function search(): void
    {
        $term  = trim($_GET['q'] ?? '');
        $senas = $this->model->all($term);
        // Al usar $this->view, las variables se extraen automáticamente
        echo $this->view('admin/senas', compact('senas', 'term'));
    }

    private function handleMediaUpload(array $file, int $idCategoria): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new \RuntimeException('Error al subir el archivo.');
    }

    if ($file['size'] > self::MAX_FILE_SIZE) {
        throw new \RuntimeException('El archivo supera el tamaño máximo permitido.');
    }

    $mime = mime_content_type($file['tmp_name']);
    if (! in_array($mime, $this->allowedMimes, true)) {
        throw new \RuntimeException('Formato de vídeo no permitido.');
    }

    // Obtener carpeta según categoría desde BD
    $subDir = $this->getCategoriaFolder($idCategoria);

    $targetDir = $this->uploadDir . $subDir . '/';
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $baseName  = basename($file['name']);
    $cleanName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $baseName);
    $dest      = $targetDir . $cleanName;

    // Aquí eliminamos la lógica que renombraba el archivo para evitar duplicados
    // Por lo tanto, si el archivo existe, será sobrescrito

    if (! move_uploaded_file($file['tmp_name'], $dest)) {
        throw new \RuntimeException('No fue posible mover el archivo subido.');
    }

    // Guardamos la ruta relativa
    return "videos/{$subDir}/{$cleanName}";
}


    public function store(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $data = [
            'id_categoria' => (int) ($_POST['id_categoria'] ?? 0),
            'palabra'     => trim($_POST['palabra'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'media_url'   => ''
        ];
        $errors = [];

        if ($data['id_categoria'] <= 0) {
            $errors[] = 'Debes seleccionar una categoría.';
        }
        if ($data['palabra'] === '') {
            $errors[] = 'La palabra es obligatoria.';
        }

        if ($errors) {
            $this->flash('error', implode(' ', $errors));
            $_SESSION['old'] = $data;
            header('Location: ' . BASE_URL . 'admin/senas?create=1');
            exit;
        }

        if (! empty($_FILES['media_url']['name'])) {
            $data['media_url'] = $this->handleMediaUpload($_FILES['media_url'], $data['id_categoria']);
        }


        try {
            $this->model->create($data);
            $this->flash('success', 'Seña creada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . 'admin/senas');
        exit;
    }

    public function edit(string $id): void
    {
        header('Location: ' . BASE_URL . "admin/senas?edit={$id}");
        exit;
    }

    public function update(string $id): void
    {
        try {
            $idInt = (int)$id;
            $existing = $this->model->find($idInt);
            if (! $existing) {
                throw new \RuntimeException('Seña no encontrada.');
            }

            $data = [
                'id_categoria' => (int) ($_POST['id_categoria'] ?? 0),
                'palabra'     => trim($_POST['palabra'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'media_url'   => $existing['media_url']
            ];
            $errors = [];

            if ($data['id_categoria'] <= 0) {
                $errors[] = 'Debes seleccionar una categoría.';
            }
            if ($data['palabra'] === '') {
                $errors[] = 'La palabra es obligatoria.';
            }

            if ($errors) {
                $this->flash('error', implode(' ', $errors));
                header('Location: ' . BASE_URL . "admin/senas?edit={$idInt}");
                exit;
            }

            if (! empty($_FILES['media_url']['name'])) {
                $newUrl = $this->handleMediaUpload($_FILES['media_url'], $data['id_categoria']);
                @unlink(ROOT_PATH . '/public/' . $existing['media_url']);
                $data['media_url'] = $newUrl;
            }


            $this->model->update($idInt, $data);
            $this->flash('success', 'Seña actualizada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        header(header: 'Location: ' . BASE_URL . 'admin/senas');
        exit;
    }

    public function delete(string $id): void
    {
        try {
            $idInt   = (int)$id;
            $sena    = $this->model->find($idInt);
            if (! $sena) {
                throw new \RuntimeException('Seña no encontrada.');
            }

            // Obtener la ruta completa del archivo para eliminarlo
            $mediaUrl = $sena['media_url'];
            if (!empty($mediaUrl)) {
                // Extraer la subcarpeta de la URL (ej. videos/catAbecedario/video.mp4 -> catAbecedario)
                preg_match('/videos\/(.*?)\//', $mediaUrl, $matches);
                $subDir = $matches[1] ?? '';
                $fullPath = ROOT_PATH . '/public/' . $mediaUrl;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $this->model->delete($idInt);
            $this->flash('success', 'Seña eliminada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . 'admin/senas');
        exit;
    }

    private function getCategoriaFolder(int $idCategoria): string
    {
        $categoriaModel = new \App\Models\Categoria();
        $categoria = $categoriaModel->find($idCategoria);

        if (! $categoria) {
            return 'otros';
        }

        // Limpiar nombre: solo letras y números
        $name = preg_replace('/[^A-Za-z0-9]/', '', $categoria['nom_categoria']);

        return 'cat' . ucfirst($name);
    }
}

