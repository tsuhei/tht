<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Test;
use App\Models\Categoria;

class TestsController extends Controller
{
    protected Test $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Test();
        // Aquí podrías validar sesión y rol ADMIN
    }

    /**
     * 1. Muestra listado, modal de creación/edición y recoge flash data
     */
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $adminName  = $_SESSION['usuario']['nom_usuario'] ?? 'Invitado';
        $tests      = $this->model->all();
        $categories = (new Categoria())->all();

        $showCreate = isset($_GET['create']);
        $showEdit   = isset($_GET['edit']);
        $showModal  = $showCreate || $showEdit;

        $test = null;
        if ($showEdit) {
            $test = $this->model->find((int) $_GET['edit']);
        }

        // Flash data: errores y valores anteriores
        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        require __DIR__ . '/../../views/admin/tests.php';
    }

    /**
     * 2. Procesa creación de test
     */
    public function store(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $nombre = trim($_POST['nombre_test'] ?? '');
        $catId  = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $errors = [];

        if (!$catId || $catId <= 0) {
            $errors[] = 'Debes seleccionar una categoría válida.';
        }
        if ($nombre === '') {
            $errors[] = 'El nombre del test es obligatorio.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = [
                'nombre_test'  => $nombre,
                'id_categoria' => $catId
            ];
            header('Location: ' . BASE_URL . 'admin/tests?create=1');
            exit;
        }

        $this->model->create([
            'nombre_test'  => $nombre,
            'id_categoria' => $catId
        ]);

        header('Location: ' . BASE_URL . 'admin/tests');
        exit;
    }

    /**
     * 3. Procesa actualización de test
     */
    public function update(string $id): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $nombre = trim($_POST['nombre_test'] ?? '');
        $catId  = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
        $errors = [];

        if (!$catId || $catId <= 0) {
            $errors[] = 'Debes seleccionar una categoría válida.';
        }
        if ($nombre === '') {
            $errors[] = 'El nombre del test no puede quedar vacío.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . "admin/tests?edit={$id}");
            exit;
        }

        $this->model->update((int) $id, [
            'nombre_test'  => $nombre,
            'id_categoria' => $catId
        ]);

        header('Location: ' . BASE_URL . 'admin/tests');
        exit;
    }

    /**
     * 4. Elimina un test
     */
    public function delete(string $id): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->model->delete((int) $id);

        header('Location: ' . BASE_URL . 'admin/tests');
        exit;
    }
}
