<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Categoria;

class CategoriasController extends Controller
{
    protected Categoria $model;

    protected string $videoUploadDir;
    protected string $iconUploadDir;

    public function __construct()
    {
        parent::__construct();

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__, 3)); // Ajusta según estructura de carpetas
        }

        $this->videoUploadDir = ROOT_PATH . '/public/videos/';
        $this->iconUploadDir  = ROOT_PATH . '/public/images/';

        $this->model = new Categoria();
        // Aquí podrías validar sesión/rol admin si lo haces en UsersController
    }

    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $adminName   = $_SESSION['usuario']['nom_usuario']
            ?? $_SESSION['usuario']['nombre']
            ?? 'Invitado';

        $term        = trim($_GET['q'] ?? '');
        $categories  = $this->model->all($term);
        $category    = null;
        $show        = false;

        if (isset($_GET['create'])) {
            $show = true;
        } elseif (isset($_GET['edit'])) {
            $show     = true;
            $category = $this->model->find((int) $_GET['edit']);
        }

        require __DIR__ . '/../../views/admin/categorias.php';
    }

    public function search(): void
    {
        $term        = trim($_GET['q'] ?? '');
        $categories  = $this->model->search($term);

        echo $this->view('admin/categorias', compact('categories', 'term'));
    }

    private function handleVideoUpload(array $file): string
    {
        $originalName = basename($file['name']);
        $cleanName    = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $originalName);

        if (!is_dir($this->videoUploadDir)) {
            mkdir($this->videoUploadDir, 0755, true);
        }

        $destPath = $this->videoUploadDir . $cleanName;
        if (file_exists($destPath)) {
            $i     = 1;
            $parts = pathinfo($cleanName);
            do {
                $cleanName = $parts['filename'] . "({$i})." . $parts['extension'];
                $destPath  = $this->videoUploadDir . $cleanName;
                $i++;
            } while (file_exists($destPath));
        }

        move_uploaded_file($file['tmp_name'], $destPath);

        return 'videos/' . $cleanName;
    }

    private function handleIconUpload(array $file): string
    {
        $originalName = basename($file['name']);
        $cleanName    = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $originalName);

        if (!is_dir($this->iconUploadDir)) {
            mkdir($this->iconUploadDir, 0755, true);
        }

        $destPath = $this->iconUploadDir . $cleanName;
        if (file_exists($destPath)) {
            $i     = 1;
            $parts = pathinfo($cleanName);
            do {
                $cleanName = $parts['filename'] . "({$i})." . $parts['extension'];
                $destPath  = $this->iconUploadDir . $cleanName;
                $i++;
            } while (file_exists($destPath));
        }

        move_uploaded_file($file['tmp_name'], $destPath);

        return 'images/' . $cleanName;
    }

    public function store(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $nomCategoria = trim($_POST['nom_categoria'] ?? '');
        $videoFile    = $_FILES['video_url'] ?? null;
        $iconFile     = $_FILES['icono'] ?? null;
        $errors       = [];

        if ($nomCategoria === '') {
            $errors[] = 'El nombre de la categoría es obligatorio.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['nom_categoria' => $nomCategoria];
            header('Location: ' . BASE_URL . 'admin/categorias?create=1');
            exit;
        }

        $videoPath = '';
        if ($videoFile && $videoFile['error'] === UPLOAD_ERR_OK) {
            $videoPath = $this->handleVideoUpload($videoFile);
        }

        $iconPath = '';
        if ($iconFile && $iconFile['error'] === UPLOAD_ERR_OK) {
            $iconPath = $this->handleIconUpload($iconFile);
        }

        try {
            $this->model->create([
                'nom_categoria' => $nomCategoria,
                'video_url'     => $videoPath,
                'icono'         => $iconPath,
            ]);
        } catch (\PDOException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                $_SESSION['errors'] = ['Nombre de categoría duplicado.'];
                $_SESSION['old']    = ['nom_categoria' => $nomCategoria];
                header('Location: ' . BASE_URL . 'admin/categorias?create=1');
                exit;
            }
            throw $e;
        }

        header('Location: ' . BASE_URL . 'admin/categorias');
        exit;
    }

    public function edit(string $id): void
    {
        header('Location: ' . BASE_URL . "admin/categorias?edit={$id}");
        exit;
    }

    public function update(string $id): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $nomCategoria = trim($_POST['nom_categoria'] ?? '');
        $videoFile    = $_FILES['video_url'] ?? null;
        $iconFile     = $_FILES['icono'] ?? null;
        $errors       = [];

        if ($nomCategoria === '') {
            $errors[] = 'El nombre de la categoría no puede quedar vacío.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . "admin/categorias?edit={$id}");
            exit;
        }

        $existing = $this->model->find((int) $id);
        $videoPath = $existing['video_url'] ?? '';
        $iconPath  = $existing['icono'] ?? '';

        if ($videoFile && $videoFile['error'] === UPLOAD_ERR_OK) {
            $videoPath = $this->handleVideoUpload($videoFile);
        }

        if ($iconFile && $iconFile['error'] === UPLOAD_ERR_OK) {
            $iconPath = $this->handleIconUpload($iconFile);
        }

        $ok = $this->model->update((int) $id, [
            'nom_categoria' => $nomCategoria,
            'video_url'     => $videoPath,
            'icono'         => $iconPath,
        ]);

        $_SESSION['flash'] = [
            'type'    => $ok ? 'success' : 'error',
            'message' => $ok
                ? 'Categoría actualizada correctamente.'
                : 'Error al actualizar categoría.'
        ];

        header('Location: ' . BASE_URL . 'admin/categorias');
        exit;
    }

    public function delete(string $id): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $category = $this->model->find((int) $id);
        if (!$category) {
            header('Location: ' . BASE_URL . 'admin/categorias');
            exit;
        }

        // Borrar archivos físicos si existen
        if (!empty($category['video_url'])) {
            @unlink($this->videoUploadDir . basename($category['video_url']));
        }
        if (!empty($category['icono'])) {
            @unlink($this->iconUploadDir . basename($category['icono']));
        }

        $this->model->delete((int) $id);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Categoría “{$category['nom_categoria']}” eliminada."
        ];

        header('Location: ' . BASE_URL . 'admin/categorias');
        exit;
    }

    public function mostrarSenas(string $id): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $categoria = $this->model->find((int) $id);
        if (!$categoria) {
            header('Location: ' . BASE_URL . 'usuarios/categorias');
            exit;
        }

        $senaModel = new \App\Models\Sena();
        $senas = $senaModel->getByCategoriaId((int) $id);

        require __DIR__ . '/../../views/usuarios/senas_user.php';
    }
}
