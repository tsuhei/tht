<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\ProgresoCategoria;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class UsersController extends Controller
{
    protected Usuario $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Usuario();
        // validación de sesión/rol admin…
    }

    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $adminName = $_SESSION['usuario']['nom_usuario']
            ?? $_SESSION['usuario']['nombre']
            ?? 'Invitado';

        $term     = $_GET['q'] ?? '';
        $usuarios = $this->model->all();
        $usuario  = null;
        $show     = false;

        require __DIR__ . '/../../views/admin/users.php';
    }

    public function search(): void
    {
        $term     = trim($_GET['q'] ?? '');
        $usuarios = $this->model->search($term);
        echo $this->view('admin/users', compact('usuarios', 'term'));
    }

    public function store(): void
    {
        // 0) Asegura que la sesión esté activa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // 1) Recoge, sanitiza y valida datos
        $idRol      = filter_input(INPUT_POST, 'id_rol', FILTER_SANITIZE_NUMBER_INT);
        $nomUsuario = trim($_POST['nom_usuario'] ?? '');
        $correo     = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
        $passPlain  = $_POST['contrasena'] ?? '';

        $errors = [];

        if (!$idRol) {
            $errors[] = 'Debe seleccionar un rol.';
        }
        if ($nomUsuario === '') {
            $errors[] = 'El nombre de usuario es obligatorio.';
        }
        if (!$correo) {
            $errors[] = 'El correo no es válido.';
        }
        if ($passPlain === '') {
            $errors[] = 'La contraseña es obligatoria.';
        }

        $userModel = new Usuario();

        // 2) Valida unicidad antes de insertar
        if ($userModel->exists('nom_usuario', $nomUsuario)) {
            $errors[] = 'El nombre de usuario ya existe.';
        }
        if ($userModel->exists('correo', $correo)) {
            $errors[] = 'El correo ya está registrado.';
        }

        // 3) Si hay errores, guárdalos en sesión y redirige a create
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = [
                'id_rol'      => $idRol,
                'nom_usuario' => $nomUsuario,
                'correo'      => $correo,
            ];

            header('Location: ' . BASE_URL . '/admin/users/create');
            exit;
        }

        // 4) Prepara datos definitivos
        $data = [
            'id_rol'      => $idRol,
            'nom_usuario' => $nomUsuario,
            'correo'      => $correo,
            'contrasena'  => password_hash($passPlain, PASSWORD_DEFAULT),
        ];

        // 5) Inserta y atrapa duplicados de BD como seguro adicional
        try {
            $userModel->create($data);
        } catch (\PDOException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                $_SESSION['errors'] = ['Usuario o correo duplicado.'];
                $_SESSION['old']    = [
                    'id_rol'      => $idRol,
                    'nom_usuario' => $nomUsuario,
                    'correo'      => $correo,
                ];

                header('Location: ' . BASE_URL . '/admin/users/create');
                exit;
            }
            throw $e;
        }

        // 6) Éxito: regresa a la lista
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    // UsersController.php

    // AdminUsersController.php
    public function edit(string $id): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $adminName = $_SESSION['usuario']['nom_usuario']
            ?? $_SESSION['usuario']['nombre']
            ?? 'Invitado';

        $usuarios = $this->model->all();
        $usuario  = $this->model->find((int)$id);
        if (! $usuario) {
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        $show = true;
        $term = '';

        require __DIR__ . '/../../views/admin/users.php';
    }



    // UsersController.php
    public function update($id)
    {
        // Arranca sesión si aún no está activa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Recoge y sanea datos del formulario
        $data = [
            'id_rol'       => filter_input(INPUT_POST, 'id_rol', FILTER_VALIDATE_INT),
            'nom_usuario'  => trim(filter_input(INPUT_POST, 'nom_usuario', FILTER_SANITIZE_STRING)),
            'correo'       => trim(filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL)),
        ];

        // Incluye contraseña cruda solo si viene no vacía
        $rawPwd = filter_input(INPUT_POST, 'contrasena', FILTER_DEFAULT);
        if (!empty($rawPwd)) {
            $data['contrasena'] = $rawPwd;
        }

        // Llama al modelo para actualizar
        $ok = $this->model->update($id, $data);

        // Opcional: flash message
        $_SESSION['success'] = $ok
            ? 'Usuario actualizado correctamente.'
            : 'No se pudo actualizar el usuario.';

        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    public function delete(string $id): void
    {
        // 1) Asegurar sesión y obtener adminName
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $adminName = $_SESSION['usuario']['nom_usuario']
            ?? $_SESSION['usuario']['nombre']
            ?? 'Invitado';

        // 2) Verificar que exista el usuario
        $usuario = $this->model->find((int)$id);
        if (! $usuario) {
            // Si no existe, redirigir sin intentar borrar
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        // 3) Ejecutar la eliminación
        $this->model->delete((int)$id);

        // 4) (Opcional) Establecer un flash message en sesión
        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Usuario “{$usuario['nom_usuario']}” eliminado."
        ];

        // 5) Redirigir de vuelta al listado
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    public function exportExcel(): void
    {
        $usuarios   = $this->model->all();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // 1. Cabeceras
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Rol');
        $sheet->setCellValue('C1', 'Usuario');
        $sheet->setCellValue('D1', 'Correo');

        // 2. Población de filas
        $fila = 2;
        foreach ($usuarios as $u) {
            $sheet->setCellValue('A' . $fila, $u['id']);
            $sheet->setCellValue('B' . $fila, $u['nom_rol']);
            $sheet->setCellValue('C' . $fila, $u['nom_usuario']);
            $sheet->setCellValue('D' . $fila, $u['correo']);
            $fila++;
        }

        // 3. Auto-size de columnas
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 4. Envío de headers HTTP y descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="usuarios.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    public function exportPdf(): void
    {
        $usuarios = $this->model->all();

        // 1. Capturar la vista en una variable
    ob_start();
    $this->view('admin/users-pdf', ['usuarios' => $usuarios]);
    $html = ob_get_clean();

        // 2. Generar PDF con Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Roboto');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // 3. Enviar al navegador
        $dompdf->stream('usuarios.pdf', ['Attachment' => true]);
        exit;
    }

        public function perfil(): void
    {
        // Datos para el layout
        $userName = $_SESSION['usuario']['nombre'] ?? 'Usuario';
        $userId   = $_SESSION['usuario']['id']; // Necesitamos el ID del usuario logueado
        $active   = 'perfil';

        // ¡Aquí llamamos a nuestra nueva función del modelo!
        $categoriasProgreso = ProgresoCategoria::getProgresoPorCategoria($userId);

        // Cargar la vista con todos los datos necesarios
        require __DIR__ . '/../../views/usuarios/perfil_user.php';
    }
}
