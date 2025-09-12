<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ProgresoCategoria;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class ProgresoCategoriaController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Validación de admin
        $usr = $_SESSION['usuario'] ?? [];
        if (empty($usr) || ($usr['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    /**
     * 1. Listado general de progresos por categoría
     */
    public function index(): void
    {
        // Definimos lo que necesita la vista progreso.php
        $adminName  = $_SESSION['usuario']['nombre'] ?? 'Administrador';
        $progresos  = \App\Models\ProgresoCategoria::getAll();

        // Cargamos la vista original progreso.php
        require __DIR__ . '/../../views/admin/progreso.php';
    }

    /**
     * 2. Detalle de progreso de un usuario específico
     */
    public function userProgress(int $userId): void
    {
        $adminName = $_SESSION['usuario']['nombre'] ?? 'Administrador';
        $detalle   = ProgresoCategoria::getByUser($userId);

        require __DIR__ . '/../../views/admin/progreso_user.php';
    }

    /**
     * 3. Exportar a Excel (XLSX)
     */
    public function exportExcel(): void
    {
        $data = ProgresoCategoria::getAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Encabezados
        $sheet->fromArray(['ID', 'Usuario', 'Categoría', 'Fecha completada'], null, 'A1');
        // Filas
        $row = 2;
        foreach ($data as $r) {
            $sheet->fromArray([
                $r['id'],
                $r['usuario'],
                $r['categoria'],
                $r['created_at']
            ], null, "A{$row}");
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="progreso.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * 4. Exportar a PDF
     */
    public function exportPDF(): void
    {
        $data = ProgresoCategoria::getAll();


        // Construir tabla HTML
        $html  = '<h2>Progreso por Categoría</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>ID</th><th>Usuario</th><th>Categoría</th><th>Fecha</th></tr>';
        foreach ($data as $r) {
            $html .= "<tr>
                <td>{$r['id']}</td>
                <td>" . htmlspecialchars($r['usuario'], ENT_QUOTES) . "</td>
                <td>" . htmlspecialchars($r['categoria'], ENT_QUOTES) . "</td>
                <td>{$r['created_at']}</td>
            </tr>";
        }
        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('progreso.pdf', ['Attachment' => 1]);
        exit;
    }
}
